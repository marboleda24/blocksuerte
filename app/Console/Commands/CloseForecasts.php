<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CloseForecasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecasts:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close daily forecasts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * close all forecasts after evaluating their viability
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $forecasts = DB::connection('MAX')
                ->table('CIEV_V_Pronosticos')
                ->where('Estado', '=', '3')
                ->pluck('NumeroPronostico');

            $state_op = [];

            foreach ($forecasts as $forecast) {
                $state_op[$forecast] = DB::connection('MAX')
                    ->table('CIEV_V_OP_Pronosticos_v1')
                    ->where('Pronostico', '=', $forecast)
                    ->select('Pronostico', 'EstadoOP')
                    ->get();
            }

            $count = [];

            foreach ($state_op as $op) {
                if (count($op) != 0) {
                    $flag = 1;

                    foreach ($op as $p) {
                        if (trim($p->EstadoOP) == 3) {
                            $flag = 0;
                        }
                    }
                    if ($flag == 1) {
                        $count[] = $op[0]->Pronostico;
                    }
                }
            }

            foreach ($count as $forecast) {
                DB::connection('MAX')->beginTransaction();

                try {
                    DB::connection('MAX')
                        ->table('Order_Master')
                        ->where('ORDNUM_10', '=', $forecast)
                        ->update([
                            'STATUS_10' => '4',
                        ]);

                    DB::connection('MAX')
                        ->table('Requirement_Detail')
                        ->where('ORDNUM_11', '=', $forecast)
                        ->update([
                            'STATUS_11' => '4',
                        ]);
                    DB::connection('MAX')->commit();
                } catch (Exception $e) {
                    DB::connection('MAX')->rollback();
                }
            }

            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail("Pronósticos cerrados", "Pronósticos cerrados con éxito", "EVPIU Le informa que los pronósticos fueron cerrados exitosamente"));
        } catch (Exception $e) {
            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail("Error en cierre de pronósticos", "Error en cierre de pronósticos", "EVPIU Le informa que hubo un error en el cierre pronósticos, por favor comuníquese con el administrador del sistema para obtener mas información"));
        }
    }
}
