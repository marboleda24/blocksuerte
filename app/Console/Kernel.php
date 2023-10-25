<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\GetTRM',
        'App\Console\Commands\CloseForecasts',
        'App\Console\Commands\CollectSensors',
        'App\Console\Commands\ClaimSensor',
        'App\Console\Commands\TariffPositionQuery',
        'App\Console\Commands\PayrollSensor',
        'App\Console\Commands\ImportDocumentMAXDMS',
        'App\Console\Commands\InvoiceAudit',
        'App\Console\Commands\SalesPerDayReport',
        'App\Console\Commands\PostmarkMailCollector',
        'App\Console\Commands\CheckCertificate',
        'App\Console\Commands\MaintenanceSchedule'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('trm:get')->dailyAt('04:00');
        $schedule->command('TPQ:update')->dailyAt('04:30');
        $schedule->command('forecasts:close')->dailyAt('06:00');
        $schedule->command('sensors:collect')->everyTenMinutes();
        $schedule->command('claim:sensor')->everyMinute();
        $schedule->command('payroll:sensor')->monthlyOn(5, '05:00');
        $schedule->command('import-documents:disabled')->lastDayOfMonth('22:00');
        $schedule->command('invoice:audit')->dailyAt('12:00');
        $schedule->command('app:sales-per-day-report')->dailyAt('16:30');
        $schedule->command('app:postmark-mail-collector')->dailyAt('12:30');
        $schedule->command('app:check-certificate')->mondays()->at('06:00');
        $schedule->command('app:maintenance-schedule')->monthlyOn(1, '06:00');
        $schedule->command('app:create-maintenance-schedule')->dailyAt('06:00');
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
