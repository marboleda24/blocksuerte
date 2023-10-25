<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportEmployeeData implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return DB::connection('GOJA')
            ->table('v_NomContratos as contrato')
            ->join('terceros_nombres as nombres', 'contrato.nit', '=', 'nombres.nit')
            ->select('contrato.ESTADO', 'contrato.nit', 'nombres.primer_nombre', 'nombres.segundo_nombre', 'nombres.primer_apellido', 'nombres.segundo_apellido', 'contrato.basico_mes')
            ->where('contrato.ESTADO', '=', 'ACTIVO')
            ->orderBy('nombres.primer_apellido')
            ->get()
            ->map(function ($row) {
                return [
                    'CC',
                    $row->nit,
                    $row->primer_apellido,
                    $row->segundo_apellido,
                    $row->primer_nombre,
                    $row->segundo_nombre,
                    '00',
                    '0',
                    $row->basico_mes,
                    '0',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'TIPO DE DOCUMENTO',
            'NUMERO DE DOCUMENTO',
            'PRIMER APELLIDO',
            'SEGUNDO APELLIDO',
            'PRIMER NOMBRE',
            'SEGUNDO NOMBRE',
            'CODIGO FONDO',
            'DIAS',
            'SALARIO',
            'VALOR DE LAS CESANTIAS',
        ];
    }
}
