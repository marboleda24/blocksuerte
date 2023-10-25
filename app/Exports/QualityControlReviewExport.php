<?php

namespace App\Exports;

use App\Models\QualityControlReview;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QualityControlReviewExport implements FromCollection, WithHeadings
{
    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'ORDEN PRODUCCION',
            'CANTIDAD INSPECCIONADA',
            'CANTIDAD CONFORME',
            'CANTIDAD NO CONFORME',
            'CAUSA',
            'OPERARIO',
            'INSPECTOR',
            'TRATAMIENTO NO CONFORMIDAD',
            'ACCIONES',
            'OBSERVACIONES',
            'CENTRO DE TRABAJO',
            'TIPO',
            'FECHA REGISTRO',
        ];
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return QualityControlReview::with('inspector', 'operator', 'cause')->get()->map(function ($row) {
            return [
                $row->production_order,
                $row->quantity_inspected,
                $row->conforming_quantity,
                $row->non_conforming_quantity,
                $row->cause->name,
                $row->operator->name,
                $row->inspector->name,
                $row->non_compliant_treatment,
                $row->actions,
                $row->observations,
                $row->work_center,
                $row->type === 'inspection' ? 'Inspeccion' : 'Producción',
                $row->created_at->format('Y-m-d'),
            ];
        });
    }
}
