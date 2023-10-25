<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use App\Models\PostmarkMailLog;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PostmarkMailCollector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:postmark-mail-collector';

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
        DB::beginTransaction();
        try {
            $time = Carbon::now()->subDays(44)->format('Y-m-d');

            $servers = [
                (object)[
                    "server" => "APIDIAN",
                    "token" => "fdf013b9-9d1e-41b0-873b-c68e4ad91b5f"
                ],
                (object)[
                    "server" => "EVPIU",
                    "token" => "a352618e-8f11-4e6c-b2f7-57650e931eb4"
                ],
            ];

            foreach ($servers as $server) {
                $client = new Client(['base_uri' => "https://api.postmarkapp.com"]);
                $response = $client->request('GET', "https://api.postmarkapp.com/messages/outbound?count=500&offset=0&todate={$time}&fromdate={$time}", [
                    'headers' => [
                        'Accept' => 'application/json',
                        'X-Postmark-Server-Token' => $server->token
                    ],
                ]);

                $result = json_decode($response->getBody()->getContents());
                foreach ($result->Messages as $message) {
                    $exist = PostmarkMailLog::where('MessageID', '=', $message->MessageID)->first();

                    $client = new Client(['base_uri' => "https://api.postmarkapp.com"]);
                    $response = $client->request('GET', "https://api.postmarkapp.com/messages/outbound/{$message->MessageID}/details", [
                        'headers' => [
                            'Accept' => 'application/json',
                            'X-Postmark-Server-Token' => $server->token
                        ],
                    ]);

                    if ($exist) {
                        $exist->update([
                            'MessageEvents' => json_decode($response->getBody()->getContents())->MessageEvents
                        ]);
                        $exist->save();
                    } else {
                        $message->MessageEvents = json_decode($response->getBody()->getContents())->MessageEvents;
                        $log = new PostmarkMailLog((array)$message);
                        $log->Server = $server->server;
                        $log->ReceivedAt = Carbon::parse($message->ReceivedAt);
                        $log->save();
                    }
                }
            }

            DB::commit();

            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Log diario de Postmark', 'Log diario de Postmark', "EVPIU le informa que el log diario de Postmark fue cargado exitosamente, mails importados: ". count($result->Messages)));
        } catch (Exception $e) {
            DB::rollBack();

            Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com'])
                ->cc('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Error log diario de Postmark', 'Error log diario de Postmark', "EVPIU le informa que hubo un error cargando el log diario de Postmark, Error: {$e->getMessage()}"));
        }
    }
}
