<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use romanzipp\QueueMonitor\Controllers\Payloads\Metric;
use romanzipp\QueueMonitor\Controllers\Payloads\Metrics;
use romanzipp\QueueMonitor\Models\Contracts\MonitorContract;
use romanzipp\QueueMonitor\Services\QueueMonitor;

class ShowQueueMonitorController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $jobs = QueueMonitor::getModel()
            ->newQuery()
            ->orderBy('job_id', 'desc')
            ->get();

        foreach ($jobs as $job) {
            if (! $job->isFinished()) {
                $job->state = 'running';
            } elseif ($job->hasSucceeded()) {
                $job->state = 'success';
            } else {
                $job->state = 'failed';
            }
        }

        $queues = QueueMonitor::getModel()
            ->newQuery()
            ->select('queue')
            ->groupBy('queue')
            ->get()
            ->map(function (MonitorContract $monitor) {
                return $monitor->queue;
            })
            ->toArray();

        $metrics = null;

        if (config('queue-monitor.ui.show_metrics')) {
            $metrics = $this->collectMetrics();
        }

        return Inertia::render('Applications/Monitor/Index', [
            'jobs' => $jobs,
            'queues' => $queues,
            'metrics' => $metrics->metrics,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function load_data()
    {
        $jobs = QueueMonitor::getModel()
            ->newQuery()
            ->orderBy('job_id', 'desc')
            ->get();

        foreach ($jobs as $job) {
            if (! $job->isFinished()) {
                $job->state = 'running';
            } elseif ($job->hasSucceeded()) {
                $job->state = 'success';
            } else {
                $job->state = 'failed';
            }
        }

        $queues = QueueMonitor::getModel()
            ->newQuery()
            ->select('queue')
            ->groupBy('queue')
            ->get()
            ->map(function (MonitorContract $monitor) {
                return $monitor->queue;
            })
            ->toArray();

        $metrics = null;

        if (config('queue-monitor.ui.show_metrics')) {
            $metrics = $this->collectMetrics();
        }

        return response()->json([
            'jobs' => $jobs,
            'queues' => $queues,
            'metrics' => $metrics->metrics,
        ], 200);
    }

    /**
     * @return Metrics
     */
    public function collectMetrics(): Metrics
    {
        $timeFrame = config('queue-monitor.ui.metrics_time_frame') ?? 2;

        $metrics = new Metrics();

        $aggregationColumns = [
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(time_elapsed) as total_time_elapsed'),
            DB::raw('AVG(time_elapsed) as average_time_elapsed'),
        ];

        $aggregatedInfo = QueueMonitor::getModel()
            ->newQuery()
            ->select($aggregationColumns)
            ->where('started_at', '>=', Carbon::now()->subDays($timeFrame))
            ->first();

        $aggregatedComparisonInfo = QueueMonitor::getModel()
            ->newQuery()
            ->select($aggregationColumns)
            ->where('started_at', '>=', Carbon::now()->subDays($timeFrame * 2))
            ->where('started_at', '<=', Carbon::now()->subDays($timeFrame))
            ->first();

        if (null === $aggregatedInfo || null === $aggregatedComparisonInfo) {
            return $metrics;
        }

        $metrics->push(new Metric('Total de trabajos ejecutados', $aggregatedInfo->count ?? 0, $aggregatedComparisonInfo->count, '%d'))
            ->push(new Metric('Tiempo total de ejecución', $aggregatedInfo->total_time_elapsed ?? 0, $aggregatedComparisonInfo->total_time_elapsed, '%ds'))
            ->push(new Metric('Tiempo promedio de ejecución', $aggregatedInfo->average_time_elapsed ?? 0, $aggregatedComparisonInfo->average_time_elapsed, '%0.2fs'));

        foreach ($metrics->metrics as $metric) {
            $metric->formated = $metric->format($metric->value ?? 0);

            if ($metric->hasChanged()) {
                if ($metric->hasIncreased()) {
                    $metric->changes = "Arriba de {$metric->format($metric->previousValue ?? 0)}";
                } else {
                    $metric->changes = "Abajo de {$metric->format($metric->previousValue ?? 0)}";
                }
            } else {
                $metric->changes = "Sin cambios desde {$metric->format($metric->previousValue ?? 0)}";
            }
        }

        return $metrics;
    }

    /**
     * @return JsonResponse
     */
    public function purge()
    {
        $model = QueueMonitor::getModel();

        $model->newQuery()->each(function (MonitorContract $monitor) {
            $monitor->delete();
        }, 200);

        return $this->load_data();
    }
}
