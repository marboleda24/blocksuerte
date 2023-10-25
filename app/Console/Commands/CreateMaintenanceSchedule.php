<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Runner\Exception;

class CreateMaintenanceSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-maintenance-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = carbon::now()->format('Y-m-d');

        $query = DB::table('V_MAINTENANCE_SCHEDULE')
            ->where('next', '=', $date)
            ->get();

        foreach ($query as $new) {
            DB::beginTransaction();
            try {
                MaintenanceRequest::create([
                    'applicant_id' => 175,
                    'asset_id' => $new->id,
                    'planning_date' => Carbon::parse($new->next),
                    'description' => 'Creado según el cronograma mensual',
                    'type' => 'preventive',
                    'state' => 1,
                ]);

                $maintenance = MaintenanceRequest::find($new->id);

                $maintenance->work_orders()
                    ->create([
                        'request_id' => $new->id,
                        'description' => 'Creado según el cronograma mensual',
                        'state' => 1,
                        'cost' => 0,
                        'type' => 'preventive',
                        'assigned_to' => 175,
                        'created_by' => 175,
                    ]);
                $maintenance->log()->create([
                    'id' => $new->id,
                    'description' => 'Se creo una orden de trabajo según el cronograma mensual',
                    'created_by' => 175,
                    'type' => 'log',
                ]);

                DB::commit();

                Mail::to(['dcorrea@estradavelasquez.com', 'dmtabares@estradavelasquez.com'])
                    ->send(new SystemNotificationMail('MANTENIMIENTOS PREVENTIVOS', 'CRONOGRAMA DE MANTENIMIENTOS PREVENTIVOS', "EVPIU le informa que se ha creado un nuevo mantenimiento preventivo para el activo $new->code-$new->name el dia $new->next según el cronograma propuesto", 'notify'));

            } catch (\Exception $e) {
                DB::rollBack();
                Mail::to(['dcorrea@estradavelasquez.com', 'dmtabares@estradavelasquez.com'])
                    ->send(new SystemNotificationMail('Error', 'Error en el cronograma de mantenimientos preventivo', "EVPIU le informa que hubo un error en la creación de los mantenimientos preventivos $e", 'notify'));
            }
        }
    }
}
