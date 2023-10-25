<?php

use App\Http\Controllers\Custom\EmployeeIncomeController;
use App\Http\Controllers\Custom\GlobalQueryController;
use App\Http\Controllers\Custom\ShowQueueMonitorController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('get-countries-dms', [GlobalQueryController::class, 'get_countries_dms'])->name('custom.global.get-countries-dms');
    Route::get('get-departments-dms/{country}', [GlobalQueryController::class, 'get_departments_dms'])->name('custom.global.get-departments-dms');
    Route::get('get-cities-dms/{country}/{department}', [GlobalQueryController::class, 'get_cities_dms'])->name('custom.global.get-cities-dms');
    Route::get('validate-provider-dms/{document}', [GlobalQueryController::class, 'validate_provider_dms'])->name('custom.global.validate-provider-dms');
    Route::post('save-provider-dms', [GlobalQueryController::class, 'save_provider'])->name('custom.global.save-provider-dms');

    Route::prefix('jobs')->group(function () {
        Route::queueMonitor();
    });

    Route::prefix('monitor')->group(function () {
        Route::get('', [ShowQueueMonitorController::class, 'index'])->name('custom.monitor.index');
        Route::get('load-data', [ShowQueueMonitorController::class, 'load_data'])->name('custom.monitor.load-data');
        Route::post('purgue', [ShowQueueMonitorController::class, 'purge'])->name('custom.monitor.purge');
    });

    Route::prefix('employee-income')->group(function () {
        Route::get('', [EmployeeIncomeController::class, 'index']);
        Route::get('check-guest/{document}', [EmployeeIncomeController::class, 'check_guest'])->name('employee-income.check-guest');
        Route::post('register-guest', [EmployeeIncomeController::class, 'register_guest'])->name('employee-income.register-guest');
    });
});
