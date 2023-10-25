<?php

namespace App\Exports\Max\Reports\Production;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PendingExport extends StringValueBinder implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, WithCustomValueBinder, ShouldAutoSize
{
    /**
     * @var object
     */
    public object $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array_values($this->data->headings);
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A3';
    }

    /**
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $lastLetter = $this->searchLetter(count($this->data->headings));

                $sheet->mergeCells("A1:{$lastLetter}1");
                $sheet->setCellValue('A1', strtoupper($this->data->title). " " .Carbon::now()->format('Y-m-d H:i:s A'));

                if ($this->data->report === 'piedra'){
                    $sheet->mergeCells("I2:J2");
                    $sheet->setCellValue('I2', 'CANTIDADES');

                    $sheet->mergeCells("L2:M2");
                    $sheet->setCellValue('L2', 'DIAS');
                }else {
                    $sheet->mergeCells("B2:C2");
                    $sheet->setCellValue('B2', 'PRODUCTO');

                    $sheet->mergeCells("H2:I2");
                    $sheet->setCellValue('H2', 'CANTIDADES');
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
                //$event->sheet->getDelegate()->getStyle("A3:{$lastLetter}3")->applyFromArray($styleArray);
            },
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
