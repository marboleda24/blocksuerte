<?php

namespace App\Traits;

use App\Models\PointOfSaleRemissionHeader;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

trait PointOfSaleRemissionTrait
{
    protected function transfer_inventory($remission_id)
    {
        DB::beginTransaction();
        try {
            $remission = PointOfSaleRemissionHeader::with('detail')
                ->find($remission_id);
        } catch (\Exception $e) {
            DB::rollBack();

            return "{$e->getCode()} – {$e->getMessage()}";
        }
    }

    /**
     * @param $consecutive
     * @return JsonResponse
     */
    protected function check_remission($consecutive): JsonResponse
    {
        try {
            $remission = PointOfSaleRemissionHeader::with('detail.lots')
                ->where('consecutive', '=', $consecutive)
                ->first();

            foreach ($remission->detail as $item) {
                if ($item->required_lot && count($item->lots) === 0) {
                    throw new Exception("No hay lotes registrados para el producto {$item->product} – {$item->description}", 500);
                }
                if ($item->required_lot && count($item->lots) > 0 && $item->lots->sum('quantity') !== $item->quantity) {
                    throw new Exception("Las cantidades de los lotes no coinciden con la cantidad total solicitada para el producto {$item->product} – {$item->description}", 500);
                }
                if (! isset($item->warehouse) || strlen($item->warehouse) === 0) {
                    throw new Exception("No se ha registrado la bodega de origen para el producto {$item->product} – {$item->description}", 500);
                }

                foreach ($item->lots as $lot) {
                    $available_quantity = DB::connection('MAX')
                        ->table('Part_Lot')
                        ->where('PRTNUM_68', '=', $item->product)
                        ->where('STK_68', '=', $item->warehouse)
                        ->where('LOTNUM_68', '=', $lot->name)
                        ->first();

                    if ($lot->quantity > intval($available_quantity->QTYOH_68)) {
                        throw new Exception("No hay suficiente stock disponible para el lote {$lot->name} en la bodega {$item->warehouse} para el producto {$item->product} – {$item->description}", 500);
                    }
                    if (intval($available_quantity->QTYOH_68) === 0) {
                        throw new Exception("No hay stock disponible para el lote {$lot->name} en la bodega {$item->warehouse} para el producto {$item->product} – {$item->description}", 500);
                    }
                }
            }

            return response()->json(true, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $consecutive
     * @return Mpdf
     *
     * @throws MpdfException
     */
    public function makePdf($consecutive): Mpdf
    {
        $remission = PointOfSaleRemissionHeader::with('user', 'detail.lots', 'order')
            ->where('consecutive', '=', $consecutive)
            ->first();

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.point_of_sale_remission.header', compact('remission')));
        $pdf->SetHTMLFooter(View::make('pdfs.point_of_sale_remission.footer'));
        $pdf->WriteHTML(View::make('pdfs.point_of_sale_remission.template', compact('remission')), HTMLParserMode::HTML_BODY);

        return $pdf;
    }

    /**
     * @return Mpdf
     *
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'fontdata' => $fontData + [
                'Roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                    'B' => 'Roboto-Bold.ttf',
                    'I' => 'Roboto-Italic.ttf',
                ],
            ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 30,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/orders/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }
}
