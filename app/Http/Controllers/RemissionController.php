<?php

namespace App\Http\Controllers;

use App\Models\RemissionDetail;
use App\Models\RemissionHeader;
use App\Models\RemissionType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class RemissionController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $remissions = RemissionHeader::with('detail.info', 'customer', 'seller', 'createdby', 'type')
            ->orderBy('id', 'desc')
            ->get();

        $remission_types = RemissionType::orderBy('description')->get();

        return Inertia::render('Applications/Remission/Index', [
            'remissions' => $remissions,
            'remission_types' => $remission_types,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $remission = new RemissionHeader($request->except('detail'));
            $remission->created_by = Auth::id();
            $remission->seller_id = Auth::id();
            $remission->state = '1';
            $remission->save();

            $remission->detail()->createMany($request->detail);

            $remission->log()->create([
                'description' => 'registro una remisión',
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            $remissions = RemissionHeader::with('detail.info', 'customer', 'seller', 'createdby', 'type')->get();

            return response()->json($remissions, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("[{$e->getCode()}]: {$e->getMessage()}", 500);
        }
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $header = RemissionHeader::find($id);
            $header->bruto = array_sum(array_column($request->detail, 'price')) * array_sum(array_column($request->detail, 'quantity'));
            $header->subtotal = array_sum(array_column($request->detail, 'price')) * array_sum(array_column($request->detail, 'quantity'));
            $header->taxes = (array_sum(array_column($request->detail, 'price')) * array_sum(array_column($request->detail, 'quantity'))) * 0.19;
            $header->save();

            foreach ($request->detail as $row) {
                RemissionDetail::where('remission_header_id', '=', $id)
                    ->where('id', '=', $row['id'])
                    ->update([
                        'quantity' => $row['quantity'],
                    ]);
            }

            $header->log()->create([
                'description' => 'modifico la remisión',
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            $remissions = RemissionHeader::with('detail.info', 'customer', 'seller', 'createdby', 'type')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($remissions, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("[{$e->getCode()}]: {$e->getMessage()}", 500);
        }
    }

    /**
     * @param $order
     * @return JsonResponse
     */
    public function order_detail($order): JsonResponse
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_EnviosPorFacturar')
            ->where('OV', '=', $order)
            ->orderBy('ENVIO')
            ->get()->map(function ($row) {
                return [
                    'code' => trim($row->REFERENCIA),
                    'description' => trim($row->PRODUCTO),
                    'quantity' => $row->CANT_DESP,
                    'price' => (float) $row->PRECIO_UND,
                    'art' => trim($row->ARTE),
                    'art2' => null,
                    'brand' => trim($row->MARCA),
                    'notes' => null,
                    'send' => $row->ENVIO,
                ];
            });

        $data = $data->groupBy('send');

        return response()->json($data);
    }

    /**
     * @param $id
     * @return string
     *
     * @throws MpdfException
     */
    public function print_pdf($id): string
    {
        $remission = RemissionHeader::with('detail.info', 'customer', 'seller', 'createdby', 'type')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.remission.header', compact('remission')));
        $pdf->SetHTMLFooter(View::make('pdfs.remission.footer', compact('remission')));
        $pdf->WriteHTML(View::make('pdfs.remission.template', compact('remission')), HTMLParserMode::HTML_BODY);

        return $pdf->Output();
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function close(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $remission = RemissionHeader::find($request->id);

            $remission->update([
                'document_support' => $request->document_support,
                'state' => '3',
            ]);

            $remission->log()->create([
                'description' => 'cerro la remisión',
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            $remissions = RemissionHeader::with('detail', 'customer', 'seller', 'createdby', 'type')->get();

            return response()->json($remissions, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json("[{$e->getCode()}]: {$e->getMessage()}", 500);
        }
    }

    /**
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
            'format' => 'Letter',
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
            'margin_top' => 33,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/remission/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @return Response
     */
    public function cellar()
    {
        return Inertia::render('Applications/Remission/CellarRemission');
    }

    public function preview_cellar_remission(Request $request)
    {
        try {
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
