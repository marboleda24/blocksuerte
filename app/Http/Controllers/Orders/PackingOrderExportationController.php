<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\PackingOrderExportationList;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;


class PackingOrderExportationController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_OV_ABIERTAS_DNL')
            ->whereIn('COD_VENDEDOR', [71, 37, 49])
            ->orderBy('OV_ITEM', 'asc')
            ->get()
            ->groupBy('RAZON_SOCIAL');

        return Inertia::render('Applications/PackingOrderExportation/Index', [
            'orders' => $data
        ]);
    }

    /**
     * @return Response
     */
    public function list()
    {
        $data = PackingOrderExportationList::with('userCreated')->get();

        return Inertia::render('Applications/PackingOrderExportation/List', [
            'data' => $data
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            PackingOrderExportationList::create([
                'user_id' => Auth::id(),
                'data' => $request->data,
                'box_list' => $request->box_list,
                'state' => 'pending'
            ]);

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $record = PackingOrderExportationList::find($id);
        $record->box_list = $request->box_list;
        $record->save();

        $data = PackingOrderExportationList::with('userCreated')->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws MpdfException
     */
    public function change_state(Request $request){
        $record = PackingOrderExportationList::find($request->id);
        $record->state = $request->state;
        $record->save();

        if ($request->state === 'close'){
            Mail::to('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail("Lista de empaque cerrada",
                    "Lista de empaque cerrada",
                    "EVPIU Le informa que la lista de empaque con consecutivo {$record->consecutive} ha sido cerrada, adjunto encontrara un archivo con toda la informacion",
                    'notify', [], $this->internal_packing_pdf($record->box_list, $record->data)));
        }

        $data = PackingOrderExportationList::with('userCreated')->get();

        return response()->json($data, 200);
    }

    /**
     * @return Response
     */
    public function pack_list()
    {
        $orders = DB::connection('MAX')
            ->table('CIEV_V_OP_OV')
            ->whereIn('Vendedor', [71, 37, 49])
            ->whereDate('FechaOV', '>', Carbon::now()->subYear()->format('Y-m-d'))
            ->where('Estado', '=', 4)
            ->get();

        $orders = $orders->groupBy('OV');

        $results = [];

        foreach ($orders as $key => $order) {
            $results[] = [
                'ov' => $key,
                'items' => array_map(function ($row) {
                    return (object)[
                        'op' => $row->OP,
                        'product' => "{$row->Referencia} – {$row->Producto}",
                        'notes' => "",
                        'brand' => "",
                        'art' => $row->Arte,
                        'art2' => "",
                        'um' => str_contains($row->Producto, 'TROQ ') ? 'Unidades' : 'Millares',
                        'quantity' => intval($row->Cant_Actual),
                        'weight' => 0,
                        'date_ov' => Carbon::parse($row->FechaOV)->format('Y-m-d'),
                        'date_op' => Carbon::parse($row->FechaOP)->format('Y-m-d')
                    ];
                }, $order->toArray()),
                'customer' => $order[0]?->Cliente,
                'address' => $order[0]?->Direccion,
                'phone' => $order[0]?->Telefono,
                'city' => $order[0]?->Ciudad,
                'country' => $order[0]?->Pais,
                'order' => $order[0]?->Pedido,
                'seller' => $order[0]?->Nombre_Vendedor
            ];
        }

        return Inertia::render('Applications/PackingOrderExportation/PackList', [
            'orders' => $results
        ]);
    }


    /**
     * @param Request $request
     * @return void
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function labels_pdf(Request $request)
    {
        $box_list = $request->boxList;
        $customer_data = $request->customerData;

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.PackingOrderExportation.Labels.header'));
        $pdf->SetHTMLFooter(View::make('pdfs.PackingOrderExportation.Labels.footer'));
        $pdf->WriteHTML(View::make('pdfs.PackingOrderExportation.Labels.template', compact('box_list', 'customer_data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param int $margin_top
     * @param string $format
     * @return Mpdf
     * @throws MpdfException
     */
    protected function initMPdf(int $margin_top = 5, string $format = 'Letter'): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => $format,
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
            'margin_top' => $margin_top,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/PackingOrderExportation/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @param Request $request
     * @return void
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function packing_pdf(Request $request)
    {
        $box_list = $request->boxList;
        $customer_data = $request->customerData;

        $pdf = $this->initMPdf(32, 'Letter-L');
        $pdf->SetHTMLHeader(View::make('pdfs.PackingOrderExportation.PackList.header'));
        $pdf->SetHTMLFooter(View::make('pdfs.PackingOrderExportation.PackList.footer'));
        $pdf->WriteHTML(View::make('pdfs.PackingOrderExportation.PackList.template', compact('box_list', 'customer_data')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param $box_list
     * @param $customer_data
     * @return Mpdf
     * @throws MpdfException
     */
    protected function internal_packing_pdf($box_list, $customer_data)
    {
        $pdf = $this->initMPdf(32, 'Letter-L');
        $pdf->SetHTMLHeader(View::make('pdfs.PackingOrderExportation.PackList.header'));
        $pdf->SetHTMLFooter(View::make('pdfs.PackingOrderExportation.PackList.footer'));
        $pdf->WriteHTML(View::make('pdfs.PackingOrderExportation.PackList.template', compact('box_list', 'customer_data')), HTMLParserMode::HTML_BODY);

        return $pdf;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show_op(Request $request){
        try {
            $query = DB::connection('MAX')
                ->table('Order_Master as OM')
                ->join('Part_Master as PM', 'OM.PRTNUM_10', '=', 'PM.PRTNUM_01')
                ->select('ORDNUM_10', 'PRTNUM_10', 'PMDES1_01', 'PMDES2_01', 'STATUS_10')
                ->where('OM.ORDNUM_10', 'LIKE', $request->op)
                ->get()->map(function ($row) {
                    $row->detail = DB::connection('MAX')
                        ->table('Job_Progress')
                        ->where('ORDNUM_14', '=', "{$row->ORDNUM_10}0000")
                        ->orderBy('OPRSEQ_14')
                        ->get();

                    return $row;
                });

            return response()->json($query, 200);
        }catch (Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
}


