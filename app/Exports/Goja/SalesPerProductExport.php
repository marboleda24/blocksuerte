<?php

namespace App\Exports\Goja;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesPerProductExport implements FromCollection, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    /**
     * @var string
     */
    public string $startDate, $endDate;

    /**
     * @param $startDate
     * @param $endDate
     */
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return DB::connection('MAXPG')->table('PG_V_FE_FacturasDetalladas')->whereBetween('FECHA', [Carbon::parse($this->startDate)->format('Y-d-m'), Carbon::parse($this->endDate)->format('Y-d-m')])->select('CodigoProducto', 'DescripcionProducto', DB::raw('SUM(Cantidad) AS quantity'), DB::raw('SUM(TotalItem) AS total'))->groupBy('CodigoProducto', 'DescripcionProducto')->get();
    }

    /**
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet;

            $sheet->mergeCells("A1:B1");
            $sheet->setCellValue('A1', 'PRODUCTO');
            $sheet->setCellValue('C1', 'CANTIDAD');
            $sheet->setCellValue('D1', 'TOTAL');

            $styleArray = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,], 'font' => ['name' => 'Calibri', 'size' => 12, 'bold' => true]];

            $event->sheet->getDelegate()->getStyle("A1:D1")->applyFromArray($styleArray);
        },];
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A2';
    }
}
