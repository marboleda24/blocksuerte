<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use App\Models\Dian\PayrollSettlement;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PayrollSensor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:sensor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'its sensor check payroll send to dian web services every 5th of month and daily';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $current_date = Carbon::now()->subMonth();

        $settlements = PayrollSettlement::with('documents')
            ->where('AÑO', '=', $current_date->year)
            ->where('MES', '=', $current_date->month)
            ->whereBetween('PERIODO', [1, 20])
            ->selectRaw('IDENTIFICACION, EMPLEADO')
            ->groupBy('IDENTIFICACION', 'EMPLEADO')
            ->get();

        $flag_pending = 0;
        $flag_success = 0;

        foreach ($settlements as $settlement) {
            $count = $settlement->documents
                ->where('year', '=', $current_date->year)
                ->where('month', '=', $current_date->month);

            $count === 0
                ? $flag_pending++
                : $flag_success++;
        }

        if ($flag_pending > 0) {
            Mail::to(['auxsistemas@estradavelasquez.com', 'sistemas@estradavelasquez.com'])
                ->bcc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Recordatorio de nomina electronica', 'Recordatorio de nomina electronica', "Le informamos que a la fecha hay $flag_pending documentos de nomina electronica pendientes por enviar a la DIAN y $flag_success documentos enviados correctamente"));
        }
    }
}
