<?php

namespace App\Exports\Report\Sell;

use App\Models\MAXInvoice;
use App\Models\MAXPGInvoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use phpseclib3\System\SSH\Agent\Identity;

class SalesPerDayExport implements FromCollection, WithHeadings
{
    public string $type;

    public string $date_range;

    public string $entity;

    /**
     * @param $type
     * @param $date_range
     * @param $entity
     */
    public function __construct($type, $date_range, $entity)
    {
        $this->type = $type;
        $this->date_range = $date_range;
        $this->entity = $entity;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $date = explode(' - ', $this->date_range);


        if($this->entity === 'CIEV ') {
            return MAXInvoice::whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where(function ($query) {
                    if ($this->type === 'CI') {
                        $query->where('TIPOCLIENTE', '=', 'CI');
                    } elseif ($this->type === 'NATIONAL') {
                        $query->whereIn('TIPOCLIENTE', ['PN', 'RC']);
                    }
                })->orderBy('NUMERO')
                ->get()
                ->map(function ($row) {
                    return [
                        $row->NUMERO,
                        $row->OV,
                        $row->OC,
                        $row->RAZONSOCIAL,
                        number_format($row->BRUTO, 2, '.', ''),
                        number_format($row->DESCUENTO, 2, '.', ''),
                        number_format($row->SUBTOTAL, 2, '.', ''),
                        $row->TIPODOC === 'CR' ? 'Memo Credito' : 'Factura',
                        Carbon::parse($row->FECHA)->format('Y-m-d'),
                    ];
                });
        }else {
            return MAXPGInvoice::whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->where(function ($query) {
                    if ($this->type === 'CI') {
                        $query->where('TIPOCLIENTE', '=', 'CI');
                    } elseif ($this->type === 'NATIONAL') {
                        $query->whereIn('TIPOCLIENTE', ['PN', 'RC']);
                    }
                })->orderBy('NUMERO')
                ->get()
                ->map(function ($row) {
                    return [
                        $row->NUMERO,
                        $row->OV,
                        $row->OC,
                        $row->RAZONSOCIAL,
                        number_format($row->BRUTO, 2, '.', ''),
                        number_format($row->DESCUENTO, 2, '.', ''),
                        number_format($row->SUBTOTAL, 2, '.', ''),
                        $row->TIPODOC === 'CR' ? 'Memo Credito' : 'Factura',
                        Carbon::parse($row->FECHA)->format('Y-m-d'),
                    ];
                });
        }
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'NUMERO',
            'OV',
            'OC',
            'RAZON SOCIAL',
            'BRUTO',
            'DESCUENTO',
            'SUBTOTAL',
            'TIPO DOCUMENTO',
            'FECHA',
        ];
    }
}
