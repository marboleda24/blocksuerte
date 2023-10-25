<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SoapClient;
use SoapFault;
use Throwable;

class GetTrm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trm:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtain daily TRM through the super financiera Webservice';

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
     * handle
     *
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $date = Carbon::now()->format('Y-m-d');

            $params = [
                'cache_wsdl' => 0,
                'trace' => 1,
                'stream_context' => stream_context_create(
                    [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ]
                ),
                'location' => 'https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService',
            ];

            $soap = new SoapClient('https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService?WSDL', $params);

            $response = $soap->queryTCRM(['tcrmQueryAssociatedDate' => $date]);
            $response = $response->return;

            $this->trm_ciev($date, $response);
            $this->trm_goja($date, $response);

        } catch (SoapFault $e) {
            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Error obteniendo TRM', 'Error obteniendo TRM', 'EVPIU Le informa que hubo un error de comunicación con el WebService Superfinanciera'));
        } catch (\Exception $e) {
            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Error obteniendo TRM', 'Error obteniendo TRM', 'EVPIU Le informa que hubo un error grabando el TRM en las bases de datos de DMS y MAX'));
        }
    }

    /**
     * @param $date
     * @param $response
     * @return void
     * @throws Throwable
     */
    protected function trm_ciev($date, $response): void
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            if ($response->success) {
                $tasa = 1 / $response->value;

                DB::connection('MAX')
                    ->table('code_master')
                    ->where('CDEKEY_36', '=', 'CURR')
                    ->where('CODE_36', '=', 'US')
                    ->update([
                        'EXCRTE_36' => $tasa,
                        'UDFKEY_36' => $response->value,
                        'UDFREF_36' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                        'ModifiedBy' => 'EVPIU',
                        'ModificationDate' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                    ]);

                DB::connection('DMS')
                    ->table('monedas_factores')
                    ->insert([
                        'moneda' => 'US',
                        'fecha' => Carbon::now()->format('Y-m-d 00:00:00'),
                        'factor' => $response->value,
                    ]);
                DB::connection('MAX')->commit();
                DB::connection('DMS')->commit();

                Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                    ->cc('dcorrea@estradavelasquez.com')
                    ->send(new SystemNotificationMail('TRM CIEV Cargado Correctamente', 'TRM Cargado Correctamente', "EVPIU le informa que el TRM del dia {$date} fue {$response->value} y fue cargado exitosamente"));
            }else {
                throw new \Exception('error', 500);
            }
        }catch (\Exception $e){
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();
        }
    }

    /**
     * @param $date
     * @param $response
     * @return void
     * @throws Throwable
     */
    protected function trm_goja($date, $response): void
    {
        DB::connection('MAXPG')->beginTransaction();
        DB::connection('GOJA')->beginTransaction();
        try {
            if ($response->success) {
                $tasa = 1 / $response->value;
                DB::connection('MAXPG')
                    ->table('code_master')
                    ->where('CDEKEY_36', '=', 'CURR')
                    ->where('CODE_36', '=', 'US')
                    ->update([
                        'EXCRTE_36' => $tasa,
                        'UDFKEY_36' => $response->value,
                        'UDFREF_36' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                        'ModifiedBy' => 'Evpiu',
                        'ModificationDate' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                    ]);

                DB::connection('GOJA')
                    ->table('monedas_factores')
                    ->insert([
                        'moneda' => 'US',
                        'fecha' => Carbon::now()->format('Y-m-d 00:00:00'),
                        'factor' => $response->value,
                    ]);

                DB::connection('MAXPG')->commit();
                DB::connection('GOJA')->commit();

                Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                    ->cc('dcorrea@estradavelasquez.com')
                    ->send(new SystemNotificationMail('TRM GOJA Cargado Correctamente', 'TRM Cargado Correctamente', "EVPIU le informa que el TRM del dia {$date} fue {$response->value} y fue cargado exitosamente"));
            }else {
                throw new \Exception('error', 500);
            }
        }catch (\Exception $e){
            DB::connection('MAXPG')->rollBack();
            DB::connection('GOJA')->rollBack();
        }
    }
}
