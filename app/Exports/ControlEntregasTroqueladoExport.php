<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ControlEntregasTroqueladoExport implements FromCollection
{
    public string $date_range;

    /**
     * @param $date_range
     */
    public function __construct($date_range)
    {
        $this->date_range = $date_range;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $date = explode(' - ', $this->date_range);

        return DB::connection('MAX')
            ->table('CIEV_V_ControlEntregasTroquelados')
            ->select('OP', 'REFERENCIA', 'COD_PROD', 'PRODUCTO', 'CANT', 'ARTE')
            ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
            ->get();
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'OP',
            'REFERENCIA',
            'CODIGO PRODUCTO',
            'PRODUCTO',
            'CANTIDAD',
            'ARTE',
        ];
    }
}
