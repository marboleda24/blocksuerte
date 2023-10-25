<?php

namespace App\Console\Commands;

use App\Models\ClaimHeader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClaimSensor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claim:sensor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * run process
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $claims = ClaimHeader::where('state', '=', 'wallet')
                ->where('accounted', '=', false)
                ->whereNotNull('credit_memo')
                ->get();

            foreach ($claims as $claim) {
                $memo = DB::connection('MAX')
                    ->table('Invoice_Master')
                    ->where('ORDNUM_31', '=', $claim->credit_memo)
                    ->first();

                if ($memo && trim($memo->INVCE_31) !== '') {
                    $claim->accounted = true;
                    $claim->credit_note = $memo->INVCE_31;
                    $claim->save();

                    $claim->logs()->create([
                        'user_id' => 175,
                        'type' => 'log',
                        'description' => 'Verificación de memo crédito exitoso',
                    ]);

                    $claim->logs()->create([
                        'user_id' => 175,
                        'type' => 'log',
                        'description' => 'reclamación lista para cerrar',
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
