<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\HeaderOrder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $orders_state = HeaderOrder::select('state', DB::raw('count(*) as total'))
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->get()->toArray();

        $orders_state = [
            'labels_files' => array_column($orders_state, 'state_name'),
            'values' => array_column($orders_state, 'total'),
        ];

        $orders_date = HeaderOrder::select(DB::raw('count(*) as total'), DB::raw('MONTH(created_at) as month'))
            ->where('state', '=', '10')
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();

        $orders_date = [
            'labels_files' => $this->getMonthName(array_column($orders_date, 'month')),
            'values' => array_column($orders_date, 'total'),
        ];

        return Inertia::render('Applications/Orders/Reports/Index', [
            'orders_state' => $orders_state,
            'orders_date' => $orders_date,
        ]);
    }

    /**
     * @param $array
     * @return array
     */
    private function getMonthName($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            switch ($value) {
                case 1:
                    $result[$key] = 'Enero';
                    break;
                case 2:
                    $result[$key] = 'Febrero';
                    break;
                case 3:
                    $result[$key] = 'Marzo';
                    break;
                case 4:
                    $result[$key] = 'Abril';
                    break;
                case 5:
                    $result[$key] = 'Mayo';
                    break;
                case 6:
                    $result[$key] = 'Junio';
                    break;
                case 7:
                    $result[$key] = 'Julio';
                    break;
                case 8:
                    $result[$key] = 'Agosto';
                    break;
                case 9:
                    $result[$key] = 'Septiembre';
                    break;
                case 10:
                    $result[$key] = 'Octubre';
                    break;
                case 11:
                    $result[$key] = 'Noviembre';
                    break;
                case 12:
                    $result[$key] = 'Diciembre';
                    break;
            }
        }

        return $result;
    }
}
