<?php

namespace App\Console\Commands;

use App\Mail\SystemNotificationMail;
use App\Models\NotificationSystem;
use App\Models\Sensor;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersRequest;
use ModbusTcpClient\Packet\ResponseFactory;

class CollectSensors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collects information from sensors by tcp';

    /**
     * attempt
     *
     * @var int
     */
    public int $attempt_gas = 0;

    /**
     * attempt
     *
     * @var int
     */
    public int $attempt_chimney1 = 0;

    /**
     * attempt
     *
     * @var int
     */
    public int $attempt_chimney2 = 0;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->GasSensor();
        $this->Chimney1();
        $this->Chimney2();
    }

    /**
     * GasSensor
     *
     * @return void
     */
    private function GasSensor(): void
    {
        $connection = BinaryStreamConnection::getBuilder()
            ->setPort(1346)
            ->setHost('192.168.1.24')
            ->setConnectTimeoutSec(1.5) // timeout when establishing connection to the server
            ->setWriteTimeoutSec(0.5) // timeout when writing/sending packet to the server
            ->setReadTimeoutSec(3) // timeout when waiting response from server
            ->build();

        $startAddress = 1003; //start address
        $quantity = 2;
        $unitID = 2; //slaveID
        $packet = new ReadInputRegistersRequest($startAddress, $quantity, $unitID);

        DB::beginTransaction();
        try {
            $binaryData = $connection->connect()->sendAndReceive($packet);
            $response = ResponseFactory::parseResponseOrThrow($binaryData);
            $responseWithStartAddress = $response->withStartAddress($startAddress);

            $gas_sensor = $responseWithStartAddress->getDoubleWordAt($startAddress)->getInt32();

            $gas_sensor = implode('.', [substr($gas_sensor, 0, -1), substr($gas_sensor, -1)]);

            Sensor::create([
                'machine' => 'GAS',
                'record' => $gas_sensor,
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::emergency('SENSOR:'.$exception);
            if ($this->attempt_gas <= 3) {
                $this->attempt_gas++;
                $this->GasSensor();
            } else {
                $this->SendMail('gas', '192.168.1.24', '1346');
            }
        } finally {
            $connection->close();
        }
    }

    /**
     * Chimney1
     *
     * @return void
     */
    private function Chimney1(): void
    {
        $connection = BinaryStreamConnection::getBuilder()
            ->setPort(1346)
            ->setHost('192.168.1.23')
            ->setConnectTimeoutSec(1.5) // timeout when establishing connection to the server
            ->setWriteTimeoutSec(0.5) // timeout when writing/sending packet to the server
            ->setReadTimeoutSec(3) // timeout when waiting response from server
            ->build();

        $startAddress = 1000; //start address
        $quantity = 1;
        $unitID = 2; //slaveID
        $packet = new ReadInputRegistersRequest($startAddress, $quantity, $unitID);
        try {
            $binaryData = $connection->connect()->sendAndReceive($packet);
            $response = ResponseFactory::parseResponseOrThrow($binaryData);
            $responseWithStartAddress = $response->withStartAddress($startAddress);
            $chimney1 = $responseWithStartAddress[$startAddress]->getBytes()[1];

            Sensor::create([
                'machine' => 'CHIMEY_1',
                'record' => $chimney1,
            ]);
        } catch (Exception $exception) {
            Log::emergency($exception);
            if ($this->attempt_chimney1 < 3) {
                $this->attempt_chimney1++;
                $this->Chimney1();
            } else {
                $this->SendMail('chimenea', '192.168.1.23', '1346');
            }
        } finally {
            $connection->close();
        }
    }

    /**
     * Chimney 2 sensor
     *
     * @return void
     */
    private function Chimney2(): void
    {
        $connection = BinaryStreamConnection::getBuilder()
            ->setPort(1346)
            ->setHost('192.168.1.23')
            ->setConnectTimeoutSec(1.5) // timeout when establishing connection to the server
            ->setWriteTimeoutSec(0.5) // timeout when writing/sending packet to the server
            ->setReadTimeoutSec(3) // timeout when waiting response from server
            ->build();

        $startAddress = 1000; //start address
        $quantity = 1;
        $unitID = 3; //slaveID
        $packet = new ReadInputRegistersRequest($startAddress, $quantity, $unitID);
        try {
            $binaryData = $connection->connect()->sendAndReceive($packet);
            $response = ResponseFactory::parseResponseOrThrow($binaryData);
            $responseWithStartAddress = $response->withStartAddress($startAddress);
            $chimney2 = $responseWithStartAddress[$startAddress]->getBytes()[1];

            Sensor::create([
                'machine' => 'CHIMEY_2',
                'record' => $chimney2,
            ]);
        } catch (Exception $exception) {
            Log::emergency($exception);
            if ($this->attempt_chimney2 < 3) {
                $this->attempt_chimney2++;
                $this->Chimney2();
            } else {
                $this->SendMail('chimenea', '192.168.1.23', '1346');
            }
        } finally {
            $connection->close();
        }
    }

    /**
     * @param $machine
     * @param $ip
     * @param $port
     * @return void
     */
    public function SendMail($machine, $ip, $port): void
    {
        $notify = NotificationSystem::where('application', 'collect-sensors')
            ->first();

        if ($notify->state) {
            Mail::to('sensores@estradavelasquez.com')
                ->send(new SystemNotificationMail("Error en sensor $machine", "Error en sensor $machine", "EVPIU le informa que hubo un error de comunicación con el sensor $machine en la IP: $ip:$port"));
        }
    }
}
