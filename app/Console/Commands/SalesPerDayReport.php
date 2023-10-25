<?php

namespace App\Console\Commands;

use App\Mail\DailyBillingReportMail;
use App\Models\MAXInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SalesPerDayReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sales-per-day-report';

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
        $date = Carbon::now();

        $national = MAXInvoice::whereBetween('FECHA', [$date->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59')])
            ->whereIn('TIPOCLIENTE', ['PN', 'RC'])
            ->select(
                DB::raw("SUM(IIF(TIPODOC = 'CU', BRUTO, -BRUTO)) AS BRUTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', DESCUENTO, -DESCUENTO)) AS DESCUENTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', SUBTOTAL, -SUBTOTAL)) AS SUBTOTAL"),
                DB::raw("SUM(IVA) AS IVA"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', (SUBTOTAL + IVA), (-SUBTOTAL + IVA))) AS TOTAL"),
            )->first();

        $ci = MAXInvoice::whereBetween('FECHA', [$date->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59')])
            ->where('TIPOCLIENTE', '=', 'CI')
            ->select(
                DB::raw("SUM(IIF(TIPODOC = 'CU', BRUTO, -BRUTO)) AS BRUTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', DESCUENTO, -DESCUENTO)) AS DESCUENTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', SUBTOTAL, -SUBTOTAL)) AS SUBTOTAL"),
                DB::raw("SUM(IVA) AS IVA"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', (SUBTOTAL + IVA), (-SUBTOTAL + IVA))) AS TOTAL"),
            )->first();

        $total = MAXInvoice::whereBetween('FECHA', [$date->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59')])
            ->whereIn('TIPOCLIENTE', ['PN', 'RC', 'CI'])
            ->select(
                DB::raw("SUM(IIF(TIPODOC = 'CU', BRUTO, -BRUTO)) AS BRUTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', DESCUENTO, -DESCUENTO)) AS DESCUENTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', SUBTOTAL, -SUBTOTAL)) AS SUBTOTAL"),
                DB::raw("SUM(IVA) AS IVA"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', (SUBTOTAL + IVA), (-SUBTOTAL + IVA))) AS TOTAL"),
            )->first();

        $current_month = MAXInvoice::whereBetween('FECHA', [Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00'), Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59')])
            ->whereIn('TIPOCLIENTE', ['PN', 'RC', 'CI'])
            ->select(
                DB::raw("SUM(IIF(TIPODOC = 'CU', BRUTO, -BRUTO)) AS BRUTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', DESCUENTO, -DESCUENTO)) AS DESCUENTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', SUBTOTAL, -SUBTOTAL)) AS SUBTOTAL"),
                DB::raw("SUM(IVA) AS IVA"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', (SUBTOTAL + IVA), (-SUBTOTAL + IVA))) AS TOTAL"),
            )->first();

        $last_year = MAXInvoice::whereBetween('FECHA', [Carbon::now()->subYear()->startOfMonth()->format('Y-m-d 00:00:00'), Carbon::now()->subYear()->endOfMonth()->format('Y-m-d 23:59:59')])
            ->whereIn('TIPOCLIENTE', ['PN', 'RC', 'CI'])
            ->select(
                DB::raw("SUM(IIF(TIPODOC = 'CU', BRUTO, -BRUTO)) AS BRUTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', DESCUENTO, -DESCUENTO)) AS DESCUENTO"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', SUBTOTAL, -SUBTOTAL)) AS SUBTOTAL"),
                DB::raw("SUM(IVA) AS IVA"),
                DB::raw("SUM(IIF(TIPODOC = 'CU', (SUBTOTAL + IVA), (-SUBTOTAL + IVA))) AS TOTAL"),
            )->first();

        Mail::to(['gerencia@estradavelasquez.com'])
            ->send(new DailyBillingReportMail($national, $ci, $total, $current_month, $last_year));
    }
}
