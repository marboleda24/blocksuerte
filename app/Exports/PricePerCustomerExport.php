<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PricePerCustomerExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = collect(array_map(function ($item) {
            return [
                $item['product'],
                $item['customer_product_code'],
                $item['price'],
                $item['new_price']
            ];
        }, $data));
    }


    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'PRODUCTO',
            'CODIGO PRODUCTO CLIENTE',
            'VALOR ACTUAL',
            'NUEVO VALOR',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 14,
                        'bold' => true
                    ]
                ];

                $event->sheet->getDelegate()->getStyle("A1:D1")->applyFromArray($styleArray);
            }
        ];
    }
}
