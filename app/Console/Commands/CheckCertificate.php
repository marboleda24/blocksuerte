<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-certificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check certificate to end date for api dian application companies';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $certificates = DB::connection('API_DIAN')
            ->table('certificates')
            ->get();

        foreach ($certificates as $certificate) {
            $pfxContent = file_get_contents(storage_path("app/certificates/{$certificate->name}"));

            if (!openssl_pkcs12_read($pfxContent, $x509certdata, $certificate->password)) {
                throw new Exception('The certificate could not be read.');
            } else {
                $CertPriv = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));

                $date = Carbon::parse($CertPriv['validTo_time_t'])->format('Y-m-d');
                $days = Carbon::now()->diffInDays($date);

                if ($days < 30) {
                    Mail::to(['dcorrea@estradavelasquez.com'])
                        ->send(new SystemNotificationMail('Certificado digital próximo a expirar',
                            'Certificado digital próximo a expirar',
                            "El certificado digital emitido para {$CertPriv['subject']['CN']} esta próximo a expirar, por favor recuerde renovarlo. \n\r Fecha de expiración: {$date}. \n\r Dias restantes: {$days}"));
                }
            }

        }
    }
}
