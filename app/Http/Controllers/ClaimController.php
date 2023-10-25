<?php

namespace App\Http\Controllers;

use App\Mail\SystemNotificationMail;
use App\Models\ClaimCause;
use App\Models\ClaimFile;
use App\Models\ClaimHeader;
use App\Models\ClaimWorkplace;
use App\Models\Dian\ApiDocument;
use App\Traits\ClaimTrait;
use App\Traits\ElectronicBillingTrait;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class ClaimController extends Controller
{
    use ClaimTrait, ElectronicBillingTrait;

    /**
     * @return Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.claim.show-all')) {
            $claims = ClaimHeader::with('user', 'invoice','remissions')->get();
        } else {
            $claims = ClaimHeader::with('user', 'invoice','remissions')
                ->where('created_id', '=', Auth::id())
                ->get();
        }

        return Inertia::render('Applications/Claim/Index', [
            'claims' => $claims,
        ]);
    }

    /**
     * @param $document
     * @return JsonResponse
     */
    public function document_data($document)
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasDetalladas')
                ->where('Factura', '=', $document)
                ->get()->map(function ($row) {
                    return [
                        'item' => $row->Item,
                        'code' => $row->CodigoProducto,
                        'description' => $row->DescripcionProducto,
                        'new_product' => '',
                        'art' => trim($row->ARTE),
                        'brand' => trim($row->Marca),
                        'price' => number_format($row->Precio, 2, ',', '.'),
                        'new_price' => null,
                        'quantity' => $row->Cantidad,
                        'new_quantity' => null,
                        'credit_note_quantity' => null,
                        'reposition_quantity' => null,
                        'delivered_quantity' => null,
                        'notes' => '',
                    ];
                });

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $destiny
     * @return JsonResponse
     */
    public function get_causes($destiny = null)
    {
        $causes = $destiny === 'cellar'
            ? ClaimCause::where('cellar', '=', true)->orderBy('name')->get()
            : ($destiny === 'quality'
                ? ClaimCause::where('quality', '=', true)->orderBy('name')->get()
                : ClaimCause::orderBy('name')->get()) ;

        return response()->json($causes, 200);
    }

    /**
     * @param Request $request
     * @param $hash
     * @return JsonResponse
     */
    public function update_causes(Request $request, $hash){
        $id = Hashids::decode($hash)[0];
        $claim = ClaimHeader::find($id);
        $claim->causes()->sync($request->causes);
        $claim->save();

        $claims = ClaimHeader::with('user', 'invoice')
            ->whereIn('state', [$claim->destiny, 'finish'])
            ->get();

        return response()->json($claims, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $claim = new ClaimHeader($request->except('files', 'items', 'causes'));
            $claim->created_id = Auth::id();
            $claim->state = 'erase';
            $claim->save();

            $claim->causes()->sync(explode(',', $request->causes));

            foreach (json_decode($request->items) as $item) {
                $claim->items()->create([
                    'item' => $item->item,
                    'product_code' => $item->code,
                    'new_product_code' => $item->new_product,
                    'new_price' => $item->new_price,
                    'credit_note_quantity' => $item->credit_note_quantity,
                    'new_quantity' => $item->new_quantity,
                    'reposition_quantity' => $item->reposition_quantity,
                    'delivered_quantity' => $item->delivered_quantity,
                    'notes' => $item->notes,
                ]);
            }

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Creo una nueva reclamacion',
            ]);

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "claims/{$claim->id}";

                    $full_path = storage_path() . "/app/claims/{$claim->id}/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::put("claims/{$claim->id}", $file);

                    if (Storage::exists($storagePath)) {
                        $claim->files()->create([
                            'name' => $filename,
                            'path' => $storagePath,
                            'type' => 'support',
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            DB::commit();
            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create()
    {
        $causes = ClaimCause::orderBy('name')->get();

        return Inertia::render('Applications/Claim/Create', [
            'causes' => $causes,
        ]);
    }

    /**
     * @param $base64
     * @return JsonResponse
     */
    public function show($base64)
    {
        $id = Hashids::decode($base64)[0];
        $claim = ClaimHeader::with('user', 'invoice', 'items.product', 'items.new_product', 'causes',
            'logs.user', 'files', 'workplace', 'new_customer','remissions')
            ->find($id);


        return response()->json($claim, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_comment(Request $request)
    {
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'comment',
                'description' => $request->msg,
            ]);


            $claim = ClaimHeader::with('logs.user')->find($id);

            Mail::to($claim['user']->email)
                ->cc($claim['user']->email)
                ->send(new SystemNotificationMail("", "RECLAMO", " EVPIU le informa que el reclamo:$claim->consecutive se añadio un nuevo comentario: $request->msg ",'notyfy'));

            return response()->json($claim->logs, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        $file = ClaimFile::find($request->id);
        return response()->download(storage_path('app/' . $file->path), $file->name);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'canceled';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Anulo el reclamo, justificación: ' . $request->justify,
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')->get();


            Mail::to($claim['user']->email)
                ->cc($claim['user']->email)
                ->send(new SystemNotificationMail("", "RECLAMO", " EVPIU le informa que el reclamo:$claim->consecutive se encuentra anulado",'notyfy'));

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);

            if ($claim->destiny === 'cellar' && $claim->reason === 'discount' || $claim->destiny === 'cellar' && $claim->reason === 'price' || $claim->destiny === 'cellar' && $claim->reason === 'major-value'){
                $claim->state = 'cost';
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Envío el reclamo al area de costos'
                ]);

                Mail::to(['costos@estradavelasquez.com'])
                    ->send(new SystemNotificationMail('Reclamo pendiente', 'Reclamo pendiente', "EVPIU le informa que el reclamo $claim->consecutive esta pendiente por gestion de costos"));
            }else {
                $claim->state = $request->destiny;
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Envío el reclamo al area de ' . $request->destiny_esp,
                ]);
            }

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice' )->get();

            Mail::to($claim['user']->email)
                ->cc($claim['user']->email)
                ->send(new SystemNotificationMail("", "RECLAMO", " EVPIU le informa que el reclamo:$claim->consecutive se encuentra en : $request->destiny_esp ",'notyfy'));

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'erase';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Re abrió el reclamo',
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')->get();

            Mail::to($claim['user']->email)
                ->cc($claim['user']->email)
                ->send(new SystemNotificationMail("", " RECLAMO ", " EVPIU le informa que el reclamo $claim->consecutive se reabrio",'notyfy'));

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function quality()
    {
        $claims = ClaimHeader::with('user', 'invoice')
            ->whereIn('state', ['quality', 'finish'])
            ->get();

        $workplaces = ClaimWorkplace::orderBy('name', 'asc')->get();

        $causes = $this->get_causes()->original;

        return Inertia::render('Applications/Claim/Quality', [
            'claims' => $claims,
            'workplaces' => $workplaces,
            'causes' => $causes
        ]);
    }

    /**
     * @param Request $request
     * @param $hash
     * @return JsonResponse
     */
    public function quality_store(Request $request, $hash)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'cellar';
            $claim->workplace_id = $request->workplace_id;
            $claim->production_order = $request->production_order;
            $claim->quality_observation = $request->quality_observation;
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Gestionó un reclamo en calidad',
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['quality', 'finish'])
                ->get();

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quality_refuse(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'refuse';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'rechazo el reclamo en calidad, justificación: ' . $request->justify,
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['quality', 'finish'])
                ->get();

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function cellar()
    {
        $claims = ClaimHeader::with('user', 'invoice')
            ->whereIn('state', ['cellar', 'finish'])
            ->get();

        $workplaces = ClaimWorkplace::orderBy('name', 'asc')->get();

        $causes = $this->get_causes()->original;

        return Inertia::render('Applications/Claim/Cellar', [
            'claims' => $claims,
            'workplaces' => $workplaces,
            'causes' => $causes
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cellar_refuse(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'refuse';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'rechazo el reclamo en bodega, justificación: ' . $request->justify,
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['cellar', 'finish'])
                ->get();

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * @return Response
     */
    public function cost(){
        $claims = ClaimHeader::with('user', 'invoice')
            ->where('state', '=', 'cost')
            ->get();

        return Inertia::render('Applications/Claim/Cost', [
            'claims' => $claims
        ]);
    }

    /**
     * @param Request $request
     * @param $hash
     * @return JsonResponse
     */
    public function cost_store(Request $request, $hash){
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'cellar';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => "Gestionó el reclamo en costos, comentarios: {$request->justify}",
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->where('state', '=', 'cost')
                ->get();

            return response()->json($claims, 200);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $hash
     * @return JsonResponse
     */
    public function cost_refuse(Request $request, $hash)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = 'refuse';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'rechazo el reclamo en costos, justificación: ' . $request->justify,
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->where('state', '=', 'cost')
                ->get();




            return response()->json($claims, 200);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function generate_credit_memo(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::with('items', 'invoice', 'user', 'causes')
                ->find($id);

            $credit_memo = $this->credit_memo($claim);

            if ($credit_memo['code'] === 200) {
                $claim->credit_memo = $credit_memo['id'];
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Genero un memo crédito',
                ]);

                DB::commit();

                return response()->json($credit_memo['id'], 200);
            } else {
                DB::rollBack();

                return response()->json($credit_memo['msg'], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getLine(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generate_remission(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::with('invoice', 'user', 'items')
                ->find($id);

            $remission = $this->remission($request->items, $claim->invoice->CLIENTE, $id, $claim->user->id);

            if ($remission['code'] === 200) {
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Genero una orden de venta',
                ]);

                DB::commit();

                return response()->json($remission['consecutive'], 200);
            } else {
                DB::rollBack();

                return response()->json($remission['msg'], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $hash
     * @return JsonResponse
     */
    public function cellar_store(Request $request, $hash)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);
            $claim->state = $claim->action === 'credit-note' || $claim->credit_memo !== null ? 'wallet' : 'finish';
            $claim->workplace_id = $request->workplace_id;
            $claim->cellar_observation = $request->cellar_observation;
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Gestionó el reclamo en bodega',
            ]);

            if ($claim->action != 'credit-note') {
                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Finalizo el reclamo',
                ]);
            }

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['cellar', 'finish'])
                ->get();

            Mail::to($claim['user']->email)
                ->cc($claim['user']->email)
                ->send(new SystemNotificationMail(" ", "RECLAMO", "EVPIU le informa que el reclamo $claim->consecutive esta finalizado  ",'notyfy'));

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function generate_sale_order(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($request->hash)[0];
            $claim = ClaimHeader::with('items', 'invoice', 'user')
                ->find($id);

            $sale_order = $this->sale_order($claim);

            if ($sale_order['code'] === 200) {
                $claim->sale_order = $sale_order['id'];
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Genero una orden de venta',
                ]);

                DB::commit();

                return response()->json($sale_order['id'], 200);
            } else {
                DB::rollBack();

                return response()->json($sale_order['msg'], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function wallet()
    {
        $claims = ClaimHeader::with('user', 'invoice')
            ->whereIn('state', ['wallet', 'finish'])
            ->get();

        return Inertia::render('Applications/Claim/Wallet', [
            'claims' => $claims,
        ]);
    }

    /**
     * @param $hash
     * @return JsonResponse
     * @throws Throwable
     */
    public function wallet_store($hash)
    {
        DB::beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);
            $result = $this->send_check_credit_note($claim->credit_memo);

            if ($result['code'] === 200 || $result['code'] === 202) {
                $claim->state = 'finish';
                $claim->save();

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Subio una nota credito a la DIAN',
                ]);

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Importo una nota credito a DMS',
                ]);

                $claim->logs()->create([
                    'user_id' => Auth::id(),
                    'type' => 'log',
                    'description' => 'Gestionó el reclamo en cartera',
                ]);

                DB::commit();
                DB::connection('DMS')->commit();

                $claims = ClaimHeader::with('user', 'invoice')
                    ->whereIn('state', ['wallet', 'finish'])
                    ->get();

                return response()->json($claims, 200);
            } else {
                DB::rollBack();
                DB::connection('DMS')->rollBack();

                return response()->json($result['msg'], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        } catch (GuzzleException $e) {
            DB::rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $credit_memo
     * @return array
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function send_check_credit_note($credit_memo)
    {
        $memo = DB::connection('MAX')
            ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
            ->where('OV', '=', $credit_memo)
            ->first();

        if ($memo && trim($memo->NUMERO) !== '') {
            $document_dian = ApiDocument::where('number', '=', trim($memo->NUMERO))
                ->where('state_document_id', '=', 1)
                ->first();

            if ($document_dian) {
                $document_dms = DB::connection('DMS')
                    ->table('documentos')
                    ->where('tipo', '=', 'NCEV')
                    ->where('numero', '=', $memo->NUMERO)
                    ->first();

                if (!$document_dms) {
                    return $this->storeDocumentDMS($credit_memo);
                } else {
                    return [
                        'msg' => 'nota credito ya subida a la DIAN y registrado en DMS',
                        'code' => 202,
                    ];
                }
            } else {
                $response_dian = json_decode($this->sendCreditNoteDian($memo->NUMERO));

                if ($response_dian->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'true') {
                    $document_dms = DB::connection('DMS')
                        ->table('documentos')
                        ->where('tipo', '=', 'NCEV')
                        ->where('numero', '=', $memo->NUMERO)
                        ->first();

                    if (!$document_dms) {
                        return $this->storeDocumentDMS($credit_memo);
                    } else {
                        return [
                            'msg' => 'nota credito ya subida a la DIAN y registrado en DMS',
                            'code' => 202,
                        ];
                    }
                } else {
                    return [
                        'msg' => 'error al subir documento a la DIAN',
                        'code' => 500,
                    ];
                }
            }
        } else {
            return [
                'msg' => 'memo credito no contabilizado',
                'code' => 500,
            ];
        }
    }

    /**
     * @param $credit_memo
     * @return array
     * @throws Throwable
     */
    protected function storeDocumentDMS($credit_memo)
    {
        DB::connection('DMS')->beginTransaction();
        try {
            $memo_header = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas_Dian')
                ->where('OV', '=', $credit_memo)
                ->first();

            $associate_vendor = DB::connection('DMS')
                ->table('terceros')
                ->where('nit', '=', explode('-', $memo_header->IDENTIFICACION))
                ->pluck('vendedor')
                ->first();

            $invoice_movements = DB::connection('DMS')
                ->table('movimiento')
                ->where('tipo', '=', 'FAC')
                ->where('numero', '=', $memo_header->OC)
                ->whereIn('seq', [1, 2])
                ->get();

            $sequence_flag = 2;

            $total_paid = ($memo_header->SUBTOTAL + $memo_header->IVA + $memo_header->FLETES + $memo_header->SEGUROS) - ($memo_header->RTEFTE + $memo_header->RTEIVA);

            DB::connection('DMS')
                ->table('documentos')
                ->insert([
                    'sw' => '23',
                    'tipo' => 'NCEV',
                    'numero' => $memo_header->NUMERO,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fecha' => $memo_header->FECHA, /* crear input para fecha */
                    'condicion' => 0,
                    'vencimiento' => $memo_header->FECHA, /*mismo valor de fecha*/
                    'valor_total' => $total_paid, /* valor pagado en RC*/
                    'iva' => $memo_header->IVA,
                    'retencion' => $memo_header->RTEFTE, /* retencion RC*/
                    'retencion_causada' => 0,
                    'retencion_iva' => $memo_header->RTEIVA,
                    'retencion_ica' => 0,
                    'descuento_pie' => $memo_header->DESCUENTO,
                    'fletes' => $memo_header->FLETES + $memo_header->SEGUROS,
                    'iva_fletes' => 0,
                    'costo' => 0,
                    'vendedor' => intval($associate_vendor), /* vendedor asociado a la factura*/
                    'valor_aplicado' => 0, /* valor pagado en RC*/
                    'anulado' => 0,
                    'modelo' => '*',
                    'documento' => $memo_header->OC, /* dejar en blanco*/
                    'notas' => $memo_header->COMENTARIOS, /* comentarios del RC*/
                    'usuario' => Auth::user()->username,
                    'pc' => gethostname(),
                    'fecha_hora' => Carbon::now(),
                    'retencion2' => 0,
                    'retencion3' => 0,
                    'bodega' => 1,
                    'impoconsumo' => 0,
                    'descuento2' => 0,
                    'duracion' => 1,
                    'concepto' => 1,
                    'vencimiento_presup' => Carbon::now(),
                    'exportado' => 1,
                    'impuesto_deporte' => 0,
                    'tasa' => $memo_header->TASA,
                    'centro_doc' => 0,
                    'valor_mercancia' => $memo_header->BRUTO,
                ]);

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'NCEV',
                    'numero' => $memo_header->NUMERO,
                    'seq' => 1,
                    'cuenta' => $invoice_movements->where('seq', '=', 1)->first()->cuenta,
                    'centro' => 0,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fec' => $memo_header->FECHA,
                    'valor' => gmp_neg($total_paid),
                    'documento' => $memo_header->OC,
                ]);

            $original_account = match ($invoice_movements->where('seq', '=', 2)->first()->cuenta) {
                '41209505', '41209521' => 41752005,
                '41209510' => 41752010,
                '41209515' => 41752015,
                '41209520' => 41752020,
            };

            DB::connection('DMS')
                ->table('movimiento')
                ->insert([
                    'tipo' => 'NCEV',
                    'numero' => $memo_header->NUMERO,
                    'seq' => 2,
                    'cuenta' => $original_account, // seq original 3 se cambia por solicitud de martin issue 1406
                    'centro' => 0,
                    'nit' => $memo_header->IDENTIFICACION,
                    'fec' => $memo_header->FECHA,
                    'valor' => $memo_header->SUBTOTAL,
                    'documento' => $memo_header->OC,
                ]);

            if ($memo_header->IVA > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCEV',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '24080521',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => $memo_header->IVA,
                        'base' => $memo_header->BASEIVA,
                        'documento' => $memo_header->OC,
                    ]);
            }

            /*
            if ($memo_header->DESCUENTO > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCEV',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '53053505',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => -abs($memo_header->DESCUENTO),
                        'documento' => $memo_header->OC,
                    ]);
            }
            */

            if ($memo_header->RTEFTE > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCEV',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '13551501',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => -abs($memo_header->RTEFTE),
                        'base' => $memo_header->BASEIVA,
                        'documento' => $memo_header->OC,
                    ]);
            }

            if ($memo_header->RTEIVA > 0) {
                $sequence_flag += 1;
                DB::connection('DMS')
                    ->table('movimiento')
                    ->insert([
                        'tipo' => 'NCEV',
                        'numero' => $memo_header->NUMERO,
                        'seq' => $sequence_flag,
                        'cuenta' => '13551705',
                        'centro' => 0,
                        'nit' => $memo_header->IDENTIFICACION,
                        'fec' => $memo_header->FECHA,
                        'valor' => -abs($memo_header->RTEIVA),
                        'documento' => $memo_header->OC,
                    ]);
            }

            DB::connection('DMS')->commit();

            return [
                'msg' => 'success store document',
                'code' => 200,
            ];
        } catch (Exception $e) {
            DB::connection('DMS')->rollBack();

            return [
                'msg' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * @param $hash
     * @return JsonResponse
     */
    public function wallet_store_without_gestion($hash)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);

            $claim->state = 'finish';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Finalizo la gestion en cartera sin acciones',
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['wallet', 'finish'])
                ->get();

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $hash
     * @return JsonResponse
     */
    public function cellar_store_without_gestion($hash)
    {
        DB::beginTransaction();
        try {
            $id = Hashids::decode($hash)[0];
            $claim = ClaimHeader::find($id);

            $claim->state = 'finish';
            $claim->save();

            $claim->logs()->create([
                'user_id' => Auth::id(),
                'type' => 'log',
                'description' => 'Finalizo la gestion en bodega sin acciones',
            ]);

            DB::commit();

            $claims = ClaimHeader::with('user', 'invoice')
                ->whereIn('state', ['cellar', 'finish'])
                ->get();

            return response()->json($claims, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @throws MpdfException|BindingResolutionException
     */
    public function print($base64)
    {
        $id = Hashids::decode($base64)[0];
        $pdf = $this->createPDF((int)$id);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
