<?php

namespace App\Exports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use stdClass;

class DeliveryControlExport implements FromCollection, WithHeadings, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    public string $title;
    public array $date;
    public stdClass $data;
    public Collection $query;

    /**
     * @param $title
     * @param $date
     * @param $data
     * @param $query
     */
    public function __construct($title, $date, $data, $query)
    {
        $this->title = $title;
        $this->date = $date;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return get_object_vars($this->data->columns);
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A2';
    }

    /**
     * @return mixed
     */
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet;

            $sheet->mergeCells("A1:E1");
            $sheet->setCellValue('A1', $this->title);


            $styleArray = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,], 'font' => ['name' => 'Calibri', 'size' => 12, 'bold' => true]];

            $event->sheet->getDelegate()->getStyle("A1:E1")->applyFromArray($styleArray);
            $event->sheet->getDelegate()->getStyle("A2:E1")->applyFromArray($styleArray);
        },];

    }
}
