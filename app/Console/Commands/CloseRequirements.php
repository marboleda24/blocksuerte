<?php

namespace App\Console\Commands;

use App\Models\HeaderOrder;
use Illuminate\Console\Command;

class CloseRequirements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requirements:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'search completed purchase orders and close the requirements';

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
     * @return bool
     */
    public function handle(): bool
    {
        try {
            $orders = HeaderOrder::orderBy('id')->get();

            foreach ($orders as $key => $order) {
                $order->consecutive = $key + 1;
                $order->save();
            }

            return true;
        } catch (\Exception $e) {
            $this->error('error construct consecutive');

            return false;
        }
    }
}
