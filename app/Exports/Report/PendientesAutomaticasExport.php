<?php

namespace App\Exports\Report;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PendientesAutomaticasExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell,ShouldAutoSize
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return DB::connection('MAX')
            ->table('CIEV_V_PendientesAutomaticas')
            ->select('OP', 'REFERENCIA', 'LOTE', 'COD_PROD', 'PRODUCTO', 'CANT_PEND', 'FECHA_LIB', 'ARTE', 'Marca', 'DIAS_OV', 'CANT_OP', 'FECHA_OV')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'OP',
            'REFERENCIA',
            'LOTE',
            'CODIGO PRODUCTO',
            'PRODUCTO',
            'CANTIDAD PENDIENTE',
            'FECHA LIBERACION',
            'ARTE',
            'MARCA',
            'DIAS OPERACION',
            'CANTIDAD OP',
            'FECHA OV',
        ];

    }

    /**
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'PENDIENTES AUTOMATICAS');

                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 12,
                        'bold' => true
                    ]
                ];

                $event->sheet->getDelegate()->getStyle('A1:M1')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A2:M2')->applyFromArray($styleArray);
            }
        ];
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A2';
    }
}
