<?php

namespace App\Exports\Report;

use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use stdClass;

class MonitoringInjectionExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell,ShouldAutoSize
{
    public array $date;

    public function __construct( $date)
    {
        $this->date = $date;
    }

    public function collection(): Collection
    {
        return DB::connection('MAX')
            ->table('CIEV_V_SeguimientoInyeccion')
            ->whereBetween('FECHA', [Carbon::parse($this->date[0])->format('Y-m-d'), Carbon::parse($this->date[1])->format('Y-m-d')])
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'OP',
            'PRODUCTO',
            'CANT',
            'ACABADO_GALV',
            'MARCA',
            'TRASABILIDAD',
            'OPERARIO',
            'MAQ',
            'FECHA',
            'SEGUIMIENTO'
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
                $sheet->setCellValue('A1', 'SEGUIMIENTO INYECCION');

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
