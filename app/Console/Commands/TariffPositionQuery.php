<?php

namespace App\Console\Commands;

use App\Models\Automation\TariffPositionQuery as TPQModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TariffPositionQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TPQ:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tariff position in MAX database';

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
     *
     * @throws \Throwable
     */
    public function handle(): int
    {
        $queries = TPQModel::all();

        DB::connection('MAX')->beginTransaction();
        try {
            foreach ($queries as $query) {
                DB::connection('MAX')
                    ->statement($query->query);
            }
            Log::info('[TPQ]: Posiciones arancelarias actualizadas con éxito');
            DB::connection('MAX')->commit();
        } catch (Exception $e) {
            Log::emergency("[TPQ]: Las posiciones arancelarias no pudieron se actualizadas [ERROR]: {$e->getMessage()}");
            DB::connection('MAX')->rollBack();
        }
    }
}
