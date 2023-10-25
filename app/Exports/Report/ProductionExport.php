<?php

namespace App\Exports\Report;

use App\Models\PendientesProduccion;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductionExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, WithColumnFormatting, ShouldAutoSize
{
    /**
     * @var string
     */
    public string $plant;

    /**
     * @param $plant
     */
    public function __construct($plant)
    {
        $this->plant = $plant;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return match ($this->plant) {
            'CNC', 'ZAMAC', 'LASER', 'ALMPT', 'UV' => PendientesProduccion::where('CT', '=', $this->plant)
                ->where('CANT_PENDIENTE', '>', 0)
                ->get()
                ->map(function ($row) {
                    return [
                        $row->OP,
                        $row->REFERENCIA,
                        $row->COD_PROD,
                        $row->PRODUCTO,
                        $row->ACABADO,
                        $row->MARCA,
                        $row->ARTE,
                        $row->CANT_COMPLETADA,
                        $row->CANT_PENDIENTE,
                        $row->DIAS_OV,
                        Carbon::parse($row->FECHA_LIBERACION)->format('Y-m-d'),
                        strlen($row->FECHA_MOV) > 0 ? Carbon::parse($row->FECHA_MOV)->format('Y-m-d'): 'N/A',
                        $row->days,
                    ];
                }),
            'STAT', 'PULIR' => PendientesProduccion::where('CT', '=', $this->plant)
                ->where('CANT_PENDIENTE', '>', 0)
                ->orderBy('DIAS_OV', 'desc')
                ->get()
                ->map(function ($row) {
                    return [
                        $row->OP,
                        $row->REFERENCIA,
                        $row->COD_PROD,
                        $row->PRODUCTO,
                        $row->ACABADO,
                        $row->MARCA,
                        $row->ARTE,
                        $row->CANT_COMPLETADA,
                        $row->CANT_PENDIENTE,
                        $row->DIAS_OV,
                        $row->DIAS_CT,
                        Carbon::parse($row->FECHA_LIBERACION)->format('Y-m-d'),
                        strlen($row->FECHA_MOV) > 0 ? Carbon::parse($row->FECHA_MOV)->format('Y-m-d'): 'N/A',
                    ];
                }),

            'PLANT', 'GALV2' => PendientesProduccion::where('CT', '=', $this->plant)
                ->where('CANT_PENDIENTE', '>', 0)
                ->orderBy('DIAS_OV', 'desc')
                ->get()
                ->map(function ($row) {
                    return [
                        $row->OP,
                        $row->REFERENCIA,
                        $row->PRODUCTO,
                        $row->ACABADO,
                        $row->MARCA,
                        $row->ARTE,
                        $row->CANT_PENDIENTE,
                        $row->DIAS_OV,
                        $row->DIAS_CT,
                    ];
                }),
            default => new Collection(),
        };
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return match ($this->plant) {
            'CNC', 'ZAMAC', 'LASER', 'ALMPT', 'UV' => [
                'OP',
                'REFERENCIA',
                'CODIGO',
                'PRODUCTO',
                'ACABADO',
                'MARCA',
                'ARTE',
                'COMPLETADA',
                'PENDIENTE',
                'FECHA',
                'DIAS OV',
                "DIAS CT",
            ],
            'STAT', 'PULIR' => [
                'OP',
                'REFERENCIA',
                'CODIGO',
                'PRODUCTO',
                'ACABADO',
                'MARCA',
                'ARTE',
                'OPERACION',
                'COMPLETADA',
                'PENDIENTE',
                'LIBERACION',
                "$this->plant",
                'DIAS',
                'PESO',
                'DIAS OV',
                'DIAS CT',
            ],
            'PLANT', 'GALV2' => [
                'OP',
                'REFERENCIA',
                'PRODUCTO',
                'ACABADO',
                'MARCA',
                'ARTE',
                'PENDIENTE',
                'DIAS OV',
                'DIAS CT',
            ],
            default => [],
        };
    }

    /**
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $lastLetter = $this->searchLetter(count($this->headings()));
                $currenDate = Carbon::now()->format('Y-m-d');

                $sheet->mergeCells("A1:{$lastLetter}1");
                $sheet->setCellValue('A1', "{$this->title($this->plant)} – $currenDate");

                if ($this->plant !== 'PLANT' && $this->plant !== 'GALV2'){
                    $sheet->mergeCells('I2:J2');
                    $sheet->setCellValue('I2', 'CANTIDADES');

                    $sheet->mergeCells('K2:L2');
                    $sheet->setCellValue('K2', 'FECHAS');
                }

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

                $event->sheet->getDelegate()->getStyle("A1:{$lastLetter}1")->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A2:{$lastLetter}2")->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A3:{$lastLetter}3")->applyFromArray($styleArray);
            },
        ];
    }

    /**
     * @param $plant
     * @return string
     */
    protected function title($plant): string
    {
        $title = match ($plant) {
            'PULIR' => "PULIDO",
            'CNC' => "CNC",
            'ZAMAC' => "ZAMAC",
            'LASER' => "LASER",
            'UV' => "UV",
            'STAT' => "ESTATICA",
            'GALV2' => "GALVANO 2",
            'ALMPT' => "INSPECCION Y EMPAQUE",
            'PLANT' => "GALVANO 1"
        };

        Carbon::setlocale(config('app.locale'));
        $date = Carbon::now()->translatedFormat("d \de F \de Y");

        return strtoupper("PENDIENTES $title – $date");
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A3';
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => DataType::TYPE_NUMERIC,
            'C' => DataType::TYPE_NUMERIC,
        ];
    }

    /**
     * @param $index
     * @return string
     */
    protected function searchLetter($index): string
    {
        return chr(65 + ($index - 1));
    }
}
