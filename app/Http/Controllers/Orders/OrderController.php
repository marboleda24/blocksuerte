<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\DetailOrder;
use App\Models\HeaderOrder;
use App\Models\LogOrder;
use App\Models\MasterOrder;
use App\Models\PriceItemOrder;
use App\Models\ProductMaxEvpiu;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Throwable;

class OrderController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.orders.show-all')) {
            $orders = DB::table('order_headers')
                ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                ->orderBy('consecutive', 'desc')
                ->get();
        } else {
            if (Auth::user()->username === 'yasuarez' || Auth::user()->username === 'mortiz' || Auth::user()->username === 'yalemos') {
                $orders = DB::table('order_headers')
                    ->where('type', 'recycling')
                    ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                    ->orderBy('consecutive', 'desc')
                    ->get();
            } else {
                $orders = DB::table('order_headers')
                    ->where('seller_id', auth()->user()->id)
                    ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                    ->orderBy('consecutive', 'desc')
                    ->get();
            }
        }

        return Inertia::render('Applications/Orders/Index', [
            'data' => $orders,
        ]);
    }

    /**
     * search customer by social reason or client code
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function search_customer(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            $queries = DB::connection('MAX')
                ->table('CIEV_V_Clientes')
                ->where('RAZON_SOCIAL', 'LIKE', '%' . $query . '%')
                ->orWhere('CODIGO_CLIENTE', 'LIKE', '%' . $query . '%')
                ->take(20)
                ->get();

            $results = [];

            foreach ($queries as $q) {
                $results[] = [
                    'name' => trim($q->RAZON_SOCIAL),
                    'code' => trim($q->CODIGO_CLIENTE),
                    'address' => trim($q->DIRECCION),
                    'city' => trim($q->CIUDAD),
                    'phone' => trim($q->TEL1),
                    'term' => trim($q->PLAZO),
                    'detained' => trim($q->ACTIVO),
                    'currency' => trim($q->MONEDA),
                    'taxable' => trim($q->GRABADO),
                    'nit' => trim($q->NIT),
                    'discount' => number_format($q->DESCUENTO, 0, '', ''),
                    'type' => trim($q->TIPO_CLIENTE),
                    'great_contributor' => $q->GRAN_CONTRIBUYENTE === '1',
                ];
            }

            return response()->json($results, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_customer_seller(Request $request)
    {
        try {
            $query = $request->get('q');
            $current_seller = User::find($request->seller);

            $queries = DB::connection('MAX')
                ->table('CIEV_V_Clientes')
                ->where('RAZON_SOCIAL', 'LIKE', '%' . $query . '%')
                ->where('VENDEDOR', 'like', $current_seller ? $current_seller->vendor_code : '%%')
                ->orWhere('CODIGO_CLIENTE', 'LIKE', '%' . $query . '%')
                ->take(20)
                ->get()
                ->map(function ($q) {
                    return [
                        'name' => trim($q->RAZON_SOCIAL),
                        'code' => trim($q->CODIGO_CLIENTE),
                        'address' => trim($q->DIRECCION),
                        'city' => trim($q->CIUDAD),
                        'phone' => trim($q->TEL1),
                        'term' => trim($q->PLAZO),
                        'detained' => trim($q->ACTIVO),
                        'currency' => trim($q->MONEDA),
                        'taxable' => trim($q->GRABADO),
                        'nit' => trim($q->NIT),
                        'discount' => number_format($q->DESCUENTO, 0, '', ''),
                    ];
                });

            return response()->json($queries, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Search arts by code art
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function search_arts(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            $queries = DB::connection('EVPIUM')
                ->table('V_Artes')
                ->where('CodigoArte', 'LIKE', '%' . $query . '%')
                ->get()
                ->map(function ($row) {
                    return [
                        'value' => trim($row->CodigoArte),
                        'label' => '',
                    ];
                });

            return response()->json($queries, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $product
     * @param $art
     * @return JsonResponse
     */
    public function verify_sold_art($product, $art): JsonResponse
    {
        $result = DB::connection('MAX')
            ->table('CIEV_V_VentasDetalladasArte')
            ->where('COD_PROD', '=', $product)
            ->where('ARTE', '=', $art)
            ->count();

        return response()->json($result, 200);
    }

    /**
     * Search brands by name
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function search_brands(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            $queries = DB::connection('EVPIUM')
                ->table('Marcas')
                ->where('NombreMarca', 'LIKE', '%' . $query . '%')
                ->get();

            $results = [];

            foreach ($queries as $q) {
                $results[] = [
                    'value' => trim($q->NombreMarca),
                    'label' => '',
                ];
            }

            return response()->json($results, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_products(Request $request): JsonResponse
    {
        try {
            $q = $request->get('q');

            $queries = ProductMaxEvpiu::where(function ($query) use ($q) {
                $query->where('code', 'LIKE', '%' . $q . '%')
                    ->orWhere('description', 'LIKE', '%' . $q . '%')
                    ->orWhere('arts', 'LIKE', '%' . $q . '%');
            })->take(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'field' => join(' – ', array_filter([$row->code, $row->description, $row->arts])),
                        'code' => $row->code,
                        'origin' => $row->origin,
                        'description' => $row->description,
                        'stock' => $row->stock,
                    ];
                });

            return response()->json($queries, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function product_customer(Request $request): JsonResponse
    {
        try {
            $query_string = $request->get('q');
            $customer = $request->get('customer');

            $result = DB::connection('MAX')
                ->table('CIEV_V_productos_clientes')
                ->where(function ($query) use ($query_string, $customer) {
                    $query->where('customer_code', '=', $customer)
                        ->where('customer_product_code', 'LIKE', "%$query_string%");
                })
                ->take(50)
                ->distinct()
                ->get()->map(function ($row) {
                    $row->field = "{$row->customer_code} - {$row->description}";

                    return $row;
                });

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Save orders
     *
     * @param mixed $request
     * @return JsonResponse
     *
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'seller' => 'required',
            'code_customer' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'tax' => 'required',
            'term' => 'required',
            'currency' => 'required',
            'notes' => 'max:90',
            'items' => 'required|array|min:1',
            'items.*' => 'required|array|distinct|min:1',
            'bruto_val' => 'required|numeric|min:0',
            'discount_val' => 'required|numeric|min:0',
            'subtotal_val' => 'required|numeric|min:0',
            'tax_val' => 'required|numeric|min:0',
            'total_val' => 'required|numeric|min:0',
            'type' => 'required',
            //'uploadedFiles.*'   => 'mimes:png,jpg,pdf,doc,docx,xlsx,xls'
        ])->validate();

        DB::beginTransaction();
        try {
            $production = [
                'items' => [],
                'totals' => [
                    'bruto' => 0,
                    'discount' => 0,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
            ];

            $cellar = [
                'items' => [],
                'totals' => [
                    'bruto' => 0,
                    'discount' => 0,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
            ];

            $dies = [
                'items' => [],
                'totals' => [
                    'bruto' => 0,
                    'discount' => 0,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
            ];

            foreach ($request->items as $item) {
                unset($item['description_product']);

                if ($item['destiny'] == 'P') {
                    $production['items'][] = $item;

                    // values
                    $bruto = $item['price'] * $item['quantity'];
                    $discount = ($request->discount * $bruto) / 100;
                    $subtotal = $bruto - $discount;
                    $tax = strval($request->tax) === '1' ? ($subtotal * 19) / 100 : 0;
                    $total = $subtotal + $tax;

                    //totals
                    $production['totals']['bruto'] += $bruto;
                    $production['totals']['discount'] += $discount;
                    $production['totals']['subtotal'] += $subtotal;
                    $production['totals']['tax'] += $tax;
                    $production['totals']['total'] += $total;
                } elseif ($item['destiny'] == 'C') {
                    $cellar['items'][] = $item;

                    // values
                    $bruto = $item['price'] * $item['quantity'];
                    $discount = ($request->discount * $bruto) / 100;
                    $subtotal = $bruto - $discount;
                    $tax = strval($request->tax) === '1' ? ($subtotal * 19) / 100 : 0;
                    $total = $subtotal + $tax;

                    //totals
                    $cellar['totals']['bruto'] += $bruto;
                    $cellar['totals']['discount'] += $discount;
                    $cellar['totals']['subtotal'] += $subtotal;
                    $cellar['totals']['tax'] += $tax;
                    $cellar['totals']['total'] += $total;
                } else {
                    $dies['items'][] = $item;

                    // values
                    $bruto = $item['price'] * $item['quantity'];
                    $discount = 0;
                    $subtotal = $bruto - $discount;
                    $tax = strval($request->tax) === '1' ? ($subtotal * 19) / 100 : 0;
                    $total = $subtotal + $tax;

                    //totals
                    $dies['totals']['bruto'] += $bruto;
                    $dies['totals']['discount'] += $discount;
                    $dies['totals']['subtotal'] += $subtotal;
                    $dies['totals']['tax'] += $tax;
                    $dies['totals']['total'] += $total;
                }
            }

            $master = MasterOrder::create();

            if (!empty($production['items']) && count($production['items']) > 0) {
                $order = HeaderOrder::create([
                    'customer_code' => $request->code_customer,
                    'notes' => strtoupper($request->notes),
                    'currency' => $request->currency,
                    'bruto' => $production['totals']['bruto'],
                    'discount' => $production['totals']['discount'],
                    'subtotal' => $production['totals']['subtotal'],
                    'taxes' => $production['totals']['tax'],
                    'total' => $production['totals']['total'],
                    'taxable' => $request->tax == 1 ? 1 : 0,
                    'created_by' => auth()->user()->id,
                    'seller_id' => $request->seller,
                    'oc' => $request->oc,
                    'state' => '1',
                    'destiny' => 'P',
                    'type' => $request->type,
                ]);

                $master->update(['production' => $order->id]);
                $order->details()->createMany($production['items']);

                $order->log()->create([
                    'description' => 'Nuevo pedido creado',
                    'type' => 'user',
                    'work_center' => 'sales',
                    'created_by' => auth()->user()->id,
                ]);
            }

            if (!empty($cellar['items']) && count($cellar['items']) > 0) {
                $order = HeaderOrder::create([
                    'consecutive' => getLastConsecutiveOrder(),
                    'customer_code' => $request->code_customer,
                    'notes' => strtoupper($request->notes),
                    'currency' => $request->currency,
                    'bruto' => $cellar['totals']['bruto'],
                    'discount' => $cellar['totals']['discount'],
                    'subtotal' => $cellar['totals']['subtotal'],
                    'taxes' => $cellar['totals']['tax'],
                    'total' => $cellar['totals']['total'],
                    'taxable' => $request->tax == 1 ? 1 : 0,
                    'created_by' => auth()->user()->id,
                    'seller_id' => $request->seller,
                    'oc' => $request->oc,
                    'state' => '1',
                    'destiny' => 'C',
                    'type' => $request->type,
                ]);

                $master->update(['cellar' => $order->id]);
                $order->details()->createMany($cellar['items']);

                $order->log()->create([
                    'description' => 'Nuevo pedido creado',
                    'type' => 'user',
                    'work_center' => 'sales',
                    'created_by' => auth()->user()->id,
                ]);
            }

            if (!empty($dies['items']) && count($dies['items']) > 0) {
                $order = HeaderOrder::create([
                    'consecutive' => getLastConsecutiveOrder(),
                    'customer_code' => $request->code_customer,
                    'notes' => strtoupper($request->notes),
                    'currency' => $request->currency,
                    'bruto' => $dies['totals']['bruto'],
                    'discount' => $dies['totals']['discount'],
                    'subtotal' => $dies['totals']['subtotal'],
                    'taxes' => $dies['totals']['tax'],
                    'total' => $dies['totals']['total'],
                    'taxable' => $request->tax == 1 ? 1 : 0,
                    'created_by' => auth()->user()->id,
                    'seller_id' => $request->seller,
                    'oc' => $request->oc,
                    'state' => '1',
                    'destiny' => 'D',
                    'type' => $request->type,
                ]);

                $master->update(['dies' => $order->id]);
                $order->details()->createMany($dies['items']);

                $order->log()->create([
                    'description' => 'Nuevo pedido creado',
                    'type' => 'user',
                    'work_center' => 'sales',
                    'created_by' => auth()->user()->id,
                ]);
            }

            foreach ($request->uploadedFiles as $file) {
                $file = $file['dataURL'];

                if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                    $file = substr($file, strpos($file, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        throw new Exception('invalid image type');
                    }
                    $file = str_replace(' ', '+', $file);
                    $file = base64_decode($file);

                    if ($file === false) {
                        throw new Exception('base64_decode failed');
                    }
                } else {
                    throw new Exception('did not match data URI with image data');
                }

                $filename = uniqid(rand(10000, 99999), false) . ".{$type}";
                $path = 'orders/master/' . $master->id;
                $fullpath = storage_path() . '/app/orders/master/' . $master->id . '/' . $filename;

                if (!Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path);
                }

                //Storage::disk('local')->put($path.$filename, $file);
                Storage::disk('local')->put('orders/master/' . $master->id . '/' . $filename, $file);

                if (file_exists($fullpath)) {
                    $master->files()->create([
                        'path' => $filename,
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json('error saving files ' . $fullpath, 500);
                }
            }
            DB::commit();
            return response()->json('Order(s) saved successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e, 500);
        }
    }

    /**
     * create new order form,
     * return only users with role seller
     *
     * @return Response
     */
    public function create(): Response
    {
        $sellers = User::where('occupation', '=', 'vendedor')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Applications/Orders/Create', [
            'sellers' => $sellers,
        ]);
    }

    /**
     * update
     *
     * @param mixed $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        Validator::make($request->all(), [
            'seller' => 'required',
            'items' => 'required|array|min:1',
            'items.*' => 'required|array|distinct|min:1',
        ]);
        DB::beginTransaction();
        try {
            $items = [
                'items' => [],
                'totals' => [
                    'bruto' => 0,
                    'discount' => 0,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
            ];

            foreach ($request->items as $item) {
                unset($item['description_product']);
                array_push($items['items'], $item);

                // values
                $bruto = $item['price'] * $item['quantity'];
                $discount = ($request->discount * $bruto) / 100;
                $subtotal = $bruto - $discount;
                $tax = strval($request->tax) === '1' ? ($subtotal * 19) / 100 : 0;
                $total = $subtotal + $tax;

                //totals
                $items['totals']['bruto'] += $bruto;
                $items['totals']['discount'] += $discount;
                $items['totals']['subtotal'] += $subtotal;
                $items['totals']['tax'] += $tax;
                $items['totals']['total'] += $total;
            }

            HeaderOrder::find($id)->update([
                'notes' => strtoupper($request->notes),
                'bruto' => $items['totals']['bruto'],
                'discount' => $items['totals']['discount'],
                'subtotal' => $items['totals']['subtotal'],
                'taxes' => $items['totals']['tax'],
                'total' => $items['totals']['total'],
                'created_by' => auth()->user()->id,
                'oc' => $request->oc,
                'state' => '1',
                'destiny' => $request->destiny,
                'type' => $request->type,
            ]);

            $order = HeaderOrder::find($id);
            $order->details()->delete();
            $order->details()->createMany($items['items']);
            $order->log()->create([
                'description' => 'Edito el pedido',
                'type' => 'user',
                'work_center' => 'sales',
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();

            return response()->json('Order(s) updated successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * send_wallet
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function send_wallet(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = HeaderOrder::with('details')->find($request->id);
            $check_products = $this->check_product($order->details);

            if (!$check_products) {
                throw new Exception('Este pedido tiene productos por creacion en MAX', 500);
            }

            if ($order->state == '1' || $order->state == '7' || $order->state == '2' && $order->substate == 'R') {
                if ($order->type === 'recycling') {
                    $order->update([
                        'state' => '4',
                        'substate' => 'P',
                    ]);

                    $order->log()->createMany([[
                        'description' => 'Pedido enviado a cartera',
                        'type' => 'work_center',
                        'work_center' => 'sales',
                        'created_by' => Auth::id(),
                    ], [
                        'description' => 'Pedido aprobado por el sistema',
                        'type' => 'system',
                        'work_center' => 'sales',
                        'created_by' => Auth::id(),
                    ], [
                        'description' => 'Pedido enviado a costos',
                        'type' => 'system',
                        'work_center' => 'wallet',
                        'created_by' => Auth::id(),
                    ], [
                        'description' => 'Pedido aprobado por el sistema',
                        'type' => 'system',
                        'work_center' => 'wallet',
                        'created_by' => Auth::id(),
                    ]]);

                } else {
                    $order->update([
                        'state' => '2',
                        'substate' => 'D',
                    ]);
                }

                DB::commit();

                if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.orders.show-all')) {
                    $orders = DB::table('order_headers')
                        ->orderBy('consecutive', 'desc')
                        ->get();
                } else {
                    $orders = DB::table('order_headers')
                        ->where('seller_id', auth()->user()->id)
                        ->orderBy('consecutive', 'desc')
                        ->get();
                }

                return response()->json($orders, 200);
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * cancel_order
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function cancel_order(Request $request): JsonResponse
    {
        try {
            $order = HeaderOrder::find($request->id);
            if ($order->state > '0' && $order->state < '4' || $order->state === '7') {
                $order->update([
                    'state' => '0',
                    'substate' => null,
                ]);

                $order->log()->create([
                    'description' => 'Pedido anulado, descripcion: ' . $request->justify,
                    'type' => 'user',
                    'work_center' => 'sales',
                    'created_by' => auth()->user()->id,
                ]);

                if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.orders.show-all')) {
                    $orders = DB::table('order_headers')
                        ->orderBy('consecutive', 'desc')
                        ->get();
                } else {
                    $orders = DB::table('order_headers')
                        ->where('seller_id', auth()->user()->id)
                        ->orderBy('consecutive', 'desc')
                        ->get();
                }

                return response()->json($orders, 200);
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * reopen_order
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function reopen_order(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = HeaderOrder::find($request->id);
            if ($order->state == 0) {
                $order->update([
                    'state' => '1',
                    'substate' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $order->log()->create([
                    'description' => 'Pedido reabierto',
                    'type' => 'user',
                    'work_center' => 'sales',
                    'created_by' => auth()->user()->id,
                ]);

                if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.orders.show-all')) {
                    $orders = DB::table('order_headers')
                        ->orderBy('consecutive', 'desc')
                        ->get();
                } else {
                    $orders = DB::table('order_headers')
                        ->where('seller_id', auth()->user()->id)
                        ->orderBy('consecutive', 'desc')
                        ->get();
                }

                DB::commit();

                return response()->json($orders, 200);
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * clone_order
     *
     * @param mixed $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function clone_order(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'customer' => 'required',
            'orderid' => 'required',
            'type' => 'required',
        ])->validate();

        DB::beginTransaction();
        try {
            $from_order = HeaderOrder::with('details')
                ->find($request->orderid);

            $to_order = $from_order->replicate()->fill([
                'customer_code' => $request->customer,
                'type' => $request->type,
                'state' => '1',
                'order_max' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'consecutive' => getLastConsecutiveOrder(),
            ]);

            $to_order->push();
            $to_order->details()->createMany($from_order->details->toArray());

            $to_order->log()->create([
                'description' => 'Nuevo pedido creado',
                'type' => 'action',
                'work_center' => 'sales',
                'created_by' => auth()->user()->id,
            ]);

            $to_order->update([
                'type' => $request->type,
            ]);

            MasterOrder::create([
                'production' => $from_order->destiny == 'P' ? $to_order->id : null,
                'cellar' => $from_order->destiny == 'C' ? $to_order->id : null,
                'dies' => $from_order->destiny == 'D' ? $to_order->id : null,
            ]);

            DB::commit();

            if (auth()->user()->hasRole('super-admin') || auth()->user()->can('application.orders.show-all')) {
                $orders = DB::table('order_headers')
                    ->orderBy('consecutive', 'desc')
                    ->get();
            } else {
                $orders = DB::table('order_headers')
                    ->where('seller_id', auth()->user()->id)
                    ->orderBy('consecutive', 'desc')
                    ->get();
            }

            return response()->json([
                'new_id' => $to_order->id,
                'orders' => $orders,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * view order modal
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function view(Request $request): JsonResponse
    {
        try {
            if ($request->id) {
                $order = HeaderOrder::with('details.product_info', 'customer', 'log', 'seller')->find($request->id);
                $order->append('retention');

                $files = MasterOrder::where($order->destiny == 'C' ? 'cellar' : ($order->destiny == 'P' ? 'production' : 'dies'), '=', $order->id)
                    ->with('files')
                    ->first();

                $order['files'] = $files->files ?? [];

                return response()->json($order, 200);
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            return response()->json($e->getFile(), 500);
        }
    }

    /**
     * log_data
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function log_data(Request $request): JsonResponse
    {
        try {
            if ($request->id) {
                $log = LogOrder::with('user')
                    ->where('order_id', '=', $request->id)
                    ->get();

                return response()->json($log, 200);
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * wallet
     *
     * @return Response
     */
    public function wallet(): Response
    {
        $orders = HeaderOrder::with('customer', 'seller')
            ->where('state', '2')
            ->where('substate', 'D')
            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
            ->orderBy('consecutive', 'desc')
            ->get();

        return Inertia::render('Applications/Orders/Wallet', [
            'data' => $orders,
        ]);
    }

    /**
     * wallet_approve
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function wallet_approve(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '2' && $order->substate == 'D') {
                    $order->update([
                        'state' => '3',
                        'substate' => 'P',
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido aprobado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'wallet',
                        'created_by' => auth()->user()->id,
                    ]);

                    $orders = HeaderOrder::with('customer')
                        ->where('state', '2')
                        ->where('substate', 'D')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    DB::commit();

                    return response()->json($orders, 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e, 500);
        }
    }

    /**
     * wallet_refuse
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function wallet_refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '2' && $order->substate == 'D') {
                    $order->update([
                        'state' => '7',
                        'substate' => null,
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido rechazado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'wallet',
                        'created_by' => auth()->user()->id,
                    ]);

                    Mail::to($order->seller->email)
                        ->send(new SystemNotificationMail("Pedido rechazado",
                            "Pedido rechazado",
                            "EVPIU Le informa que el pedido con consecutivo: {$order->consecutive} ha sido rechazado desde el area de cartera por el usuario " . auth()->user()->name . ", motivo: {$request->justify}"));

                    $orders = HeaderOrder::with('customer')
                        ->where('state', '2')
                        ->where('substate', 'D')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    DB::commit();

                    return response()->json($orders, 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * costs
     *
     * @return Response
     */
    public function costs(): Response
    {
        $orders = HeaderOrder::with('customer', 'seller')
            ->where('state', '3')
            ->where('substate', 'P')
            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
            ->orderBy('consecutive', 'desc')
            ->get();

        return Inertia::render('Applications/Orders/Costs', [
            'data' => $orders,
        ]);
    }

    /**
     * production
     *
     * @return Response
     */
    public function cellar(): Response
    {
        $orders = DB::table('order_headers')
            ->where('state', '=', '4')
            ->where('substate', '=', 'P')
            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
            ->orderBy('consecutive', 'desc')
            ->get();

        $finished = DB::table('order_headers')
            ->where('state', '=', '10')
            ->orderBy('consecutive', 'desc')
            ->get();

        return Inertia::render('Applications/Orders/Cellar', [
            'data' => $orders,
            'finished' => $finished,
        ]);
    }

    /**
     * production
     *
     * @return Response
     */
    public function production(): Response
    {
        $orders = DB::table('order_headers')
            ->where('state', '=', '5')
            ->where('substate', '=', 'P')
            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
            ->orderBy('consecutive', 'desc')
            ->get();

        $finished = DB::table('order_headers')
            ->where('state', '=', '10')
            ->orderBy('consecutive', 'desc')
            ->get();

        return Inertia::render('Applications/Orders/Production', [
            'data' => $orders,
            'finished' => $finished,
        ]);
    }

    /**
     * production
     *
     * @return Response
     */
    public function dies(): Response
    {
        $orders = DB::table('order_headers')
            ->where('state', '=', '6')
            ->where('substate', '=', 'P')
            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
            ->orderBy('consecutive', 'desc')
            ->get();

        $finished = DB::table('order_headers')
            ->where('state', '=', '10')
            ->orderBy('consecutive', 'desc')
            ->get();

        return Inertia::render('Applications/Orders/Dies', [
            'data' => $orders,
            'finished' => $finished,
        ]);
    }

    /**
     * approve order by costs
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function costs_approve(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '3' && $order->substate == 'P') {
                    if ($order->destiny == 'C') {
                        $order->state = '4';
                        $order->substate = 'P';
                        $order->save();

                        $order->log()->create([
                            'description' => 'Pedido aprobado, descripcion: ' . $request->justify,
                            'type' => 'user',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->log()->create([
                            'description' => 'Pedido enviado a bodega',
                            'type' => 'work_center',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);
                    } elseif ($order->destiny == 'P') {
                        $order->state = '5';
                        $order->substate = 'P';
                        $order->save();

                        $order->log()->create([
                            'description' => 'Pedido aprobado, descripcion: ' . $request->justify,
                            'type' => 'user',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->log()->create([
                            'description' => 'Pedido enviado a produccion',
                            'type' => 'work_center',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);
                    } elseif ($order->destiny == 'D') {
                        $order->state = '6';
                        $order->substate = 'P';
                        $order->save();

                        $order->log()->create([
                            'description' => 'Pedido aprobado, descripcion: ' . $request->justify,
                            'type' => 'user',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->log()->create([
                            'description' => 'Pedido enviado a troqueles',
                            'type' => 'work_center',
                            'work_center' => 'costs',
                            'created_by' => auth()->user()->id,
                        ]);
                    }

                    foreach ($order->details as $item) {
                        PriceItemOrder::updateOrCreate([
                            'customer_code' => $order->customer_code,
                            'product' => $item->product,
                            'customer_product_code' => $item->customer_product_code,
                        ], [
                            'price' => $item->price,
                            'approved_by' => auth()->user()->id,
                            'state' => 1,
                        ]);
                    }

                    $orders = HeaderOrder::with('customer', 'seller')
                        ->where('state', '3')
                        ->where('substate', 'P')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    DB::commit();

                    return response()->json($orders, 200);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * costs_refuse
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function costs_refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '3' && $order->substate == 'P') {
                    $order->update([
                        'state' => '7',
                        'substate' => null,
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido rechazado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'costs',
                        'created_by' => auth()->user()->id,
                    ]);

                    Mail::to($order->seller->email)
                        ->send(new SystemNotificationMail("Pedido rechazado",
                            "Pedido rechazado",
                            "EVPIU Le informa que el pedido con consecutivo: {$order->consecutive} ha sido rechazado desde el area de costos por el usuario " . auth()->user()->name . ", motivo: {$request->justify}"));

                    $orders = HeaderOrder::with('customer')
                        ->where('state', '3')
                        ->where('substate', 'P')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    DB::commit();

                    return response()->json($orders, 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cellar_refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '4' && $order->substate == 'P') {
                    $order->update([
                        'state' => '7',
                        'substate' => null,
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido rechazado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'cellar',
                        'created_by' => auth()->user()->id,
                    ]);

                    DB::commit();

                    Mail::to($order->seller->email)
                        ->send(new SystemNotificationMail("Pedido rechazado",
                            "Pedido rechazado",
                            "EVPIU Le informa que el pedido con consecutivo: {$order->consecutive} ha sido rechazado desde el area de bodega por el usuario " . auth()->user()->name . ", motivo: {$request->justify}"));

                    $orders = DB::table('order_headers')
                        ->where('state', '=', '4')
                        ->where('substate', '=', 'P')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    $finished = DB::table('order_headers')
                        ->where('state', '=', '10')
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    return response()->json([
                        'data' => $orders,
                        'finished' => $finished,
                    ], 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * production_refuse
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function production_refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '5' && $order->substate == 'P') {
                    $order->update([
                        'state' => '7',
                        'substate' => null,
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido rechazado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'production',
                        'created_by' => auth()->user()->id,
                    ]);

                    DB::commit();

                    Mail::to($order->seller->email)
                        ->send(new SystemNotificationMail("Pedido rechazado",
                            "Pedido rechazado",
                            "EVPIU Le informa que el pedido con consecutivo: {$order->consecutive} ha sido rechazado desde el area de produccion por el usuario " . auth()->user()->name . ", motivo: {$request->justify}"));

                    $orders = DB::table('order_headers')
                        ->where('state', '=', '5')
                        ->where('substate', '=', 'P')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    $finished = DB::table('order_headers')
                        ->where('state', '=', '10')
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    return response()->json([
                        'orders' => $orders,
                        'finished' => $finished,
                    ], 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * dies_refuse
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function dies_refuse(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::find($request->id);

                if ($order->state == '6' && $order->substate == 'P') {
                    $order->update([
                        'state' => '7',
                        'substate' => null,
                    ]);

                    $order->log()->create([
                        'description' => 'Pedido rechazado, descripcion: ' . $request->justify,
                        'type' => 'user',
                        'work_center' => 'dies',
                        'created_by' => auth()->user()->id,
                    ]);

                    DB::commit();

                    Mail::to($order->seller->email)
                        ->send(new SystemNotificationMail("Pedido rechazado",
                            "Pedido rechazado",
                            "EVPIU Le informa que el pedido con consecutivo: {$order->consecutive} ha sido rechazado desde el area de troqueles por el usuario " . auth()->user()->name . ", motivo: {$request->justify}"));

                    $orders = DB::table('order_headers')
                        ->where('state', '=', '6')
                        ->where('substate', '=', 'P')
                        ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    $finished = DB::table('order_headers')
                        ->where('state', '=', '10')
                        ->orderBy('consecutive', 'desc')
                        ->get();

                    return response()->json([
                        'orders' => $orders,
                        'finished' => $finished,
                    ], 200);
                } else {
                    return response()->json('unprocessable entity', 422);
                }
            } else {
                return response()->json('unprocessable entity', 422);
            }
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
    public function finalize_order(Request $request): JsonResponse
    {
        DB::beginTransaction();
        DB::connection('MAX')->beginTransaction();
        try {
            if ($request->id && $request->justify) {
                $order = HeaderOrder::with('customer', 'details', 'seller')->find($request->id);

                if ($order->state === '10') {
                    return response()->json('unprocessable entity', 422);
                }

                $max_order_num = DB::connection('MAX')
                        ->table('SO_Master')
                        ->whereIn('STYPE_27', ['CU', 'CR'])
                        ->max('ORDNUM_27') + 1;

                $note_1 = '';
                $note_2 = '';
                $note_3 = '';
                $notes_header = strlen($order->notes) > 30 ? str_split($order->notes, 30) : $order->notes;

                if (is_array($notes_header)) {
                    if (array_key_exists(0, $notes_header)) {
                        $note_1 = $notes_header[0];
                    }
                    if (array_key_exists(1, $notes_header)) {
                        $note_2 = $notes_header[1];
                    }
                    if (array_key_exists(2, $notes_header)) {
                        $note_3 = $notes_header[2];
                    }
                } else {
                    $note_1 = $notes_header;
                }

                switch ($order->type) {
                    case 'national':
                    case 'recycling':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => '41209505',
                                'STYPE_27' => 'CU',
                                'STATUS_27' => '3',
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => trim($order->customer_master->COMMIS_23),
                                'TERMS_27' => trim($order->customer_master->TERMS_23),
                                'SHPVIA_27' => trim($order->customer_master->SHPVIA_23),
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => trim($order->customer_master->CITY_23),
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => $order->type === 'recycling' ? '38' : ($order->destiny == 'C' ? '23' : ($order->destiny == 'P' ? '22' : $request->dies_reason)),
                                'NAME_27' => trim($order->customer_master->NAME_23),
                                'ADDR1_27' => trim($order->customer_master->ADDR1_23),
                                'ADDR2_27' => trim($order->customer_master->ADDR2_23),
                                'CITY_27' => trim($order->customer_master->CITY_23),
                                'STATE_27' => trim($order->customer_master->STATE_23),
                                'ZIPCD_27' => trim($order->customer_master->ZIPCD_23),
                                'CNTRY_27' => trim($order->customer_master->CNTRY_23),
                                'PHONE_27' => trim($order->customer_master->PHONE_23),
                                'CNTCT_27' => trim($order->customer_master->CNTCT_23),
                                'TAXPRV_27' => trim($order->customer_master->TAXPRV_23),
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => $order->taxes, /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => trim($order->customer_master->ADDR3_23),
                                'ADDR4_27' => trim($order->customer_master->ADDR4_23),
                                'ADDR5_27' => trim($order->customer_master->ADDR5_23),
                                'ADDR6_27' => trim($order->customer_master->ADDR6_23),
                                'MCOMP_27' => trim($order->customer_master->MCOMP_23),
                                'MSITE_27' => trim($order->customer_master->MSITE_23),
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);
                            $prtnum = DB::connection('MAX')->table('Part_Master')->where('PRTNUM_01', '=', $item->product)->first();
                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);
                            $cellar = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('STK_29')->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('QTYCOM_29')->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'nationalUSD':
                    case 'export':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => 41209505,
                                'STYPE_27' => 'CU',
                                'STATUS_27' => 3,
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => $order->customer_master->COMMIS_23,
                                'TERMS_27' => $order->customer_master->TERMS_23,
                                'SHPVIA_27' => $order->customer_master->SHPVIA_23,
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => substr($order->customer_master->CITY_23, 0, 14),
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => $order->type === 'nationalUSD' ? '39' : '27',
                                'NAME_27' => $order->customer_master->NAME_23,
                                'ADDR1_27' => $order->customer_master->ADDR1_23,
                                'ADDR2_27' => $order->customer_master->ADDR2_23,
                                'CITY_27' => $order->customer_master->CITY_23,
                                'STATE_27' => $order->customer_master->STATE_23,
                                'ZIPCD_27' => $order->customer_master->ZIPCD_23,
                                'CNTRY_27' => $order->customer_master->CNTRY_23,
                                'PHONE_27' => $order->customer_master->PHONE_23,
                                'CNTCT_27' => $order->customer_master->CNTCT_23,
                                'TAXPRV_27' => $order->customer_master->TAXPRV_23,
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => DB::connection('MAX')->table('Code_Master')->where('CDEKEY_36', '=', 'CURR')->where('CODE_36', '=', 'US')->pluck('EXCRTE_36')->first(),
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => $order->taxes,
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => $order->customer_master->ADDR3_23,
                                'ADDR4_27' => $order->customer_master->ADDR4_23,
                                'ADDR5_27' => $order->customer_master->ADDR5_23,
                                'ADDR6_27' => $order->customer_master->ADDR6_23,
                                'MCOMP_27' => $order->customer_master->MCOMP_23,
                                'MSITE_27' => $order->customer_master->MSITE_23,
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);

                            $prtnum = DB::connection('MAX')
                                ->table('Part_Master')
                                ->where('PRTNUM_01', '=', $item->product)
                                ->first();

                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                            $factor = DB::connection('MAX')
                                ->table('Code_Master')
                                ->where('CDEKEY_36', '=', 'CURR')
                                ->where('CODE_36', '=', 'US')
                                ->pluck('EXCRTE_36')
                                ->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->unit_measurement == 'units' ? floatval($item->price / floatval($factor)) : floatval(($item->price / 1000) / floatval($factor)),
                                    'ORGQTY_28' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'CURQTY_28' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => 'PEXPO', /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->unit_measurement == 'units' ? floatval($item->price) : floatval($item->price / 1000),
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => 0,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => floatval($item->price),
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => $order->taxable == 1 ? 'Y' : 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_10' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('QTYCOM_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'ORGQTY_11' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'DUEQTY_11' => $item->unit_measurement == 'units' ? floatval($item->quantity) : floatval($item->quantity * 1000),
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }

                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripción: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'forecast':
                        foreach ($order->details as $item) {
                            $prtnum = DB::connection('MAX')
                                ->table('Part_Master')
                                ->where('PRTNUM_01', '=', $item->product)
                                ->first();

                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                            $max_order_forecast = DB::connection('MAX')
                                    ->table('Order_Master')
                                    ->where('TYPE_10', '=', 'FC')
                                    ->max('ORDNUM_10') + 1;

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_forecast,
                                    'LINNUM_10' => '00',
                                    'DELNUM_10' => '00',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'FC',
                                    'ORDER_10' => $max_order_num . '0000',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $order->consecutive,
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $order->customer_code,
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_forecast,
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'FC',
                                    'ORDNUM_11' => $max_order_forecast,
                                    'LINNUM_11' => '00',
                                    'DELNUM_11' => '00',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_forecast . '0000',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }

                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_forecast;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_forecast,
                        ], 200);
                    case 'samples':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => 41209505,
                                'STYPE_27' => 'CU',
                                'STATUS_27' => 3,
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => $order->customer_master->COMMIS_23,
                                'TERMS_27' => $order->customer_master->TERMS_23,
                                'SHPVIA_27' => $order->customer_master->SHPVIA_23,
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => $order->customer_master->CITY_23,
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => '20', // 23 si es bodega
                                'NAME_27' => $order->customer_master->NAME_23,
                                'ADDR1_27' => $order->customer_master->ADDR1_23,
                                'ADDR2_27' => $order->customer_master->ADDR2_23,
                                'CITY_27' => $order->customer_master->CITY_23,
                                'STATE_27' => $order->customer_master->STATE_23,
                                'ZIPCD_27' => $order->customer_master->ZIPCD_23,
                                'CNTRY_27' => $order->customer_master->CNTRY_23,
                                'PHONE_27' => $order->customer_master->PHONE_23,
                                'CNTCT_27' => $order->customer_master->CNTCT_23,
                                'TAXPRV_27' => $order->customer_master->TAXPRV_23,
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => '', /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => $order->customer_master->ADDR3_23,
                                'ADDR4_27' => $order->customer_master->ADDR4_23,
                                'ADDR5_27' => $order->customer_master->ADDR5_23,
                                'ADDR6_27' => $order->customer_master->ADDR6_23,
                                'MCOMP_27' => $order->customer_master->MCOMP_23,
                                'MSITE_27' => $order->customer_master->MSITE_23,
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);

                            $prtnum = DB::connection('MAX')
                                ->table('Part_Master')
                                ->where('PRTNUM_01', '=', $item->product)
                                ->first();

                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                            $cellar = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('STK_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('QTYCOM_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'elena':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => 41209505,
                                'STYPE_27' => 'CU',
                                'STATUS_27' => 3,
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => $order->customer_master->COMMIS_23,
                                'TERMS_27' => $order->customer_master->TERMS_23,
                                'SHPVIA_27' => $order->customer_master->SHPVIA_23,
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => $order->customer_master->CITY_23,
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => '26', // 23 si es bodega
                                'NAME_27' => $order->customer_master->NAME_23,
                                'ADDR1_27' => $order->customer_master->ADDR1_23,
                                'ADDR2_27' => $order->customer_master->ADDR2_23,
                                'CITY_27' => $order->customer_master->CITY_23,
                                'STATE_27' => $order->customer_master->STATE_23,
                                'ZIPCD_27' => $order->customer_master->ZIPCD_23,
                                'CNTRY_27' => $order->customer_master->CNTRY_23,
                                'PHONE_27' => $order->customer_master->PHONE_23,
                                'CNTCT_27' => $order->customer_master->CNTCT_23,
                                'TAXPRV_27' => $order->customer_master->TAXPRV_23,
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => '', /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => $order->customer_master->ADDR3_23,
                                'ADDR4_27' => $order->customer_master->ADDR4_23,
                                'ADDR5_27' => $order->customer_master->ADDR5_23,
                                'ADDR6_27' => $order->customer_master->ADDR6_23,
                                'MCOMP_27' => $order->customer_master->MCOMP_23,
                                'MSITE_27' => $order->customer_master->MSITE_23,
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);
                            $prtnum = DB::connection('MAX')->table('Part_Master')->where('PRTNUM_01', '=', $item->product)->first();
                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);
                            $cellar = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('STK_29')->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('QTYCOM_29')->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'point_of_sale':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => 41209505,
                                'STYPE_27' => 'CU',
                                'STATUS_27' => 3,
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => $order->customer_master->COMMIS_23,
                                'TERMS_27' => $order->customer_master->TERMS_23,
                                'SHPVIA_27' => $order->customer_master->SHPVIA_23,
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => $order->customer_master->CITY_23,
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => $request->point_of_sale_reason, // 23 si es bodega
                                'NAME_27' => $order->customer_master->NAME_23,
                                'ADDR1_27' => $order->customer_master->ADDR1_23,
                                'ADDR2_27' => $order->customer_master->ADDR2_23,
                                'CITY_27' => $order->customer_master->CITY_23,
                                'STATE_27' => $order->customer_master->STATE_23,
                                'ZIPCD_27' => $order->customer_master->ZIPCD_23,
                                'CNTRY_27' => $order->customer_master->CNTRY_23,
                                'PHONE_27' => $order->customer_master->PHONE_23,
                                'CNTCT_27' => $order->customer_master->CNTCT_23,
                                'TAXPRV_27' => $order->customer_master->TAXPRV_23,
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => '', /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => $order->customer_master->ADDR3_23,
                                'ADDR4_27' => $order->customer_master->ADDR4_23,
                                'ADDR5_27' => $order->customer_master->ADDR5_23,
                                'ADDR6_27' => $order->customer_master->ADDR6_23,
                                'MCOMP_27' => $order->customer_master->MCOMP_23,
                                'MSITE_27' => $order->customer_master->MSITE_23,
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);

                            $prtnum = DB::connection('MAX')
                                ->table('Part_Master')
                                ->where('PRTNUM_01', '=', $item->product)
                                ->first();

                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                            $cellar = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('STK_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $cellar,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('QTYCOM_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'services':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => '41209505',
                                'STYPE_27' => 'CU',
                                'STATUS_27' => '3',
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => trim($order->customer_master->COMMIS_23),
                                'TERMS_27' => trim($order->customer_master->TERMS_23),
                                'SHPVIA_27' => trim($order->customer_master->SHPVIA_23),
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => trim($order->customer_master->CITY_23),
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => '24',
                                'NAME_27' => trim($order->customer_master->NAME_23),
                                'ADDR1_27' => trim($order->customer_master->ADDR1_23),
                                'ADDR2_27' => trim($order->customer_master->ADDR2_23),
                                'CITY_27' => trim($order->customer_master->CITY_23),
                                'STATE_27' => trim($order->customer_master->STATE_23),
                                'ZIPCD_27' => trim($order->customer_master->ZIPCD_23),
                                'CNTRY_27' => trim($order->customer_master->CNTRY_23),
                                'PHONE_27' => trim($order->customer_master->PHONE_23),
                                'CNTCT_27' => trim($order->customer_master->CNTCT_23),
                                'TAXPRV_27' => trim($order->customer_master->TAXPRV_23),
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => $order->taxes, /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => trim($order->customer_master->ADDR3_23),
                                'ADDR4_27' => trim($order->customer_master->ADDR4_23),
                                'ADDR5_27' => trim($order->customer_master->ADDR5_23),
                                'ADDR6_27' => trim($order->customer_master->ADDR6_23),
                                'MCOMP_27' => trim($order->customer_master->MCOMP_23),
                                'MSITE_27' => trim($order->customer_master->MSITE_23),
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);
                            $prtnum = DB::connection('MAX')->table('Part_Master')->where('PRTNUM_01', '=', $item->product)->first();
                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);
                            $cellar = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('STK_29')->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('QTYCOM_29')->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'delivered_merchandise':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => '41209505',
                                'STYPE_27' => 'CU',
                                'STATUS_27' => '3',
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => trim($order->customer_master->COMMIS_23),
                                'TERMS_27' => trim($order->customer_master->TERMS_23),
                                'SHPVIA_27' => trim($order->customer_master->SHPVIA_23),
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => trim($order->customer_master->CITY_23),
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => '07', // 23 si es bodega
                                'NAME_27' => trim($order->customer_master->NAME_23),
                                'ADDR1_27' => trim($order->customer_master->ADDR1_23),
                                'ADDR2_27' => trim($order->customer_master->ADDR2_23),
                                'CITY_27' => trim($order->customer_master->CITY_23),
                                'STATE_27' => trim($order->customer_master->STATE_23),
                                'ZIPCD_27' => trim($order->customer_master->ZIPCD_23),
                                'CNTRY_27' => trim($order->customer_master->CNTRY_23),
                                'PHONE_27' => trim($order->customer_master->PHONE_23),
                                'CNTCT_27' => trim($order->customer_master->CNTCT_23),
                                'TAXPRV_27' => trim($order->customer_master->TAXPRV_23),
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => $order->taxes, /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => trim($order->customer_master->ADDR3_23),
                                'ADDR4_27' => trim($order->customer_master->ADDR4_23),
                                'ADDR5_27' => trim($order->customer_master->ADDR5_23),
                                'ADDR6_27' => trim($order->customer_master->ADDR6_23),
                                'MCOMP_27' => trim($order->customer_master->MCOMP_23),
                                'MSITE_27' => trim($order->customer_master->MSITE_23),
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        $cellar_stk = match ($request->point_of_sale_reason) {
                            '30' => 'ITAGUI',
                            '31' => 'CALI',
                            '33' => 'BOGOTA',
                            '35' => 'PEREIRA',
                        };

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);

                            $prtnum = DB::connection('MAX')->table('Part_Master')
                                ->where('PRTNUM_01', '=', $item->product)
                                ->first();

                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar_stk, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->pluck('QTYCOM_29')
                                ->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => '01',
                                                'DELNUM_30' => $linum,
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }

                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';

                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    case 'claim':
                        DB::connection('MAX')
                            ->table('SO_Master')
                            ->insert([
                                'ORDNUM_27' => $max_order_num,
                                'CUSTID_27' => $order->customer_code,
                                'GLXREF_27' => '41209505',
                                'STYPE_27' => 'CU',
                                'STATUS_27' => '3',
                                'CUSTPO_27' => $order->oc ?? '',
                                'ORDID_27' => $order->consecutive,
                                'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'FILL01A_27' => '', /*empty*/
                                'FILL01_27' => '', /*empty*/
                                'SHPCDE_27' => '', /*empty*/
                                'REP1_27' => $order->seller->vendor_code ?? trim($order->customer_master->SLSREP_23),
                                'SPLIT1_27' => 100,
                                'REP2_27' => '', /*empty*/
                                'SPLIT2_27' => 0,
                                'REP3_27' => '', /*empty*/
                                'SPLIT3_27' => 0,
                                'COMMIS_27' => trim($order->customer_master->COMMIS_23),
                                'TERMS_27' => trim($order->customer_master->TERMS_23),
                                'SHPVIA_27' => trim($order->customer_master->SHPVIA_23),
                                'XURR_27' => '', /*empty*/
                                'FOB_27' => trim($order->customer_master->CITY_23),
                                'TAXCD1_27' => $order->taxable == 1 ? 'IVA-V19' : '',
                                'TAXCD2_27' => '', /*empty*/
                                'TAXCD3_27' => '', /*empty*/
                                'COMNT1_27' => $note_1,
                                'COMNT2_27' => $note_2,
                                'COMNT3_27' => $note_3,
                                'SHPLBL_27' => 0,
                                'INVCE_27' => 'N',
                                'APPINV_27' => '', /*empty*/
                                'REASON_27' => '23',
                                'NAME_27' => trim($order->customer_master->NAME_23),
                                'ADDR1_27' => trim($order->customer_master->ADDR1_23),
                                'ADDR2_27' => trim($order->customer_master->ADDR2_23),
                                'CITY_27' => trim($order->customer_master->CITY_23),
                                'STATE_27' => trim($order->customer_master->STATE_23),
                                'ZIPCD_27' => trim($order->customer_master->ZIPCD_23),
                                'CNTRY_27' => trim($order->customer_master->CNTRY_23),
                                'PHONE_27' => trim($order->customer_master->PHONE_23),
                                'CNTCT_27' => trim($order->customer_master->CNTCT_23),
                                'TAXPRV_27' => trim($order->customer_master->TAXPRV_23),
                                'FEDTAX_27' => 'N',
                                'TAXABL_27' => $order->taxable == 1 ? 'Y' : 'N',
                                'EXCRTE_27' => 1,
                                'FIXVAR_27' => 'V',
                                'CURR_27' => $order->currency,
                                'RCLDTE_27' => null,
                                'FILL02_27' => '', /*empty*/
                                'TTAX_27' => $order->taxes, /*empty*/
                                'LNETAX_27' => 'N',
                                'ADDR3_27' => trim($order->customer_master->ADDR3_23),
                                'ADDR4_27' => trim($order->customer_master->ADDR4_23),
                                'ADDR5_27' => trim($order->customer_master->ADDR5_23),
                                'ADDR6_27' => trim($order->customer_master->ADDR6_23),
                                'MCOMP_27' => trim($order->customer_master->MCOMP_23),
                                'MSITE_27' => trim($order->customer_master->MSITE_23),
                                'UDFKEY_27' => '', /*empty*/
                                'UDFREF_27' => '', /*empty*/
                                'SHPTHRU_27' => '', /*empty*/
                                'XDFINT_27' => 0,
                                'XDFFLT_27' => 0,
                                'XDFBOL_27' => '', /*empty*/
                                'XDFDTE_27' => null, /*empty*/
                                'XDFTXT_27' => '', /*empty*/
                                'FILLER_27' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                'CreationDate' => null,
                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                'ModificationDate' => null,
                                'BILLCDE_27' => '', /*empty*/
                            ]);

                        foreach ($order->details as $idx => $item) {
                            $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);
                            $prtnum = DB::connection('MAX')->table('Part_Master')->where('PRTNUM_01', '=', $item->product)->first();
                            $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);
                            $cellar = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('STK_29')->first();

                            DB::connection('MAX')
                                ->table('SO_Detail')
                                ->insert([
                                    'ORDNUM_28' => $max_order_num,
                                    'LINNUM_28' => $linum,
                                    'DELNUM_28' => '01',
                                    'STATUS_28' => 3,
                                    'CUSTID_28' => $order->customer_code,
                                    'PRTNUM_28' => $item->product,
                                    'EDILIN_28' => '', /*empty*/
                                    'TAXABL_28' => $order->taxable == 1 ? 'Y' : 'N',
                                    'GLXREF_28' => 61209505,
                                    'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'), /*empty*/
                                    'QTLINE_28' => '', /*empty*/
                                    'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'QTDEL_28' => '', /*empty*/
                                    'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'PROBAB_28' => 0,
                                    'SHPDTE_28' => null,  /*empty*/
                                    'FILL04_28' => '', /*empty*/
                                    'SLSUOM_28' => 'UN',
                                    'REFRNC_28' => $max_order_num . $linum . '01',
                                    'PRICE_28' => $item->price,
                                    'ORGQTY_28' => $item->quantity,
                                    'CURQTY_28' => $item->quantity,
                                    'BCKQTY_28' => 0,
                                    'SHPQTY_28' => 0,
                                    'DUEQTY_28' => $item->quantity,
                                    'INVQTY_28' => 0,
                                    'DISC_28' => 0,
                                    'STYPE_28' => 'CU',
                                    'PRNT_28' => 'N',
                                    'AKPRNT_28' => 'N',
                                    'STK_28' => $cellar, /*empty*/
                                    'COCFLG_28' => '', /*empty*/
                                    'FORCUR_28' => $item->price,
                                    'HSTAT_28' => 'R',
                                    'SLSREP_28' => '', /*empty*/
                                    'COMMIS_28' => 0,
                                    'DRPSHP_28' => '', /*empty*/
                                    'QUMQTY_28' => 0,
                                    'TAXCDE1_28' => $order->taxable == 1 ? 'IVA-V19' : '',
                                    'TAX1_28' => $order->taxable == 1 ? ($item->price * $item->quantity) * 0.19 : 0,
                                    'TAXCDE2_28' => '', /*empty*/
                                    'TAX2_28' => 0,
                                    'TAXCDE3_28' => '', /*empty*/
                                    'TAX3_28' => 0,
                                    'MCOMP_28' => '', /*empty*/
                                    'MSITE_28' => '', /*empty*/
                                    'UDFKEY_28' => $item->type === 'new' ? 'N' : '', /*empty*/
                                    'UDFREF_28' => '', /*empty*/
                                    'DEXPFLG_28' => 'N',
                                    'COST_28' => $prtnum->COST_01,
                                    'MARKUP_28' => 0,
                                    'QTORD_28' => '', /*empty*/
                                    'XDFINT_28' => 0,
                                    'XDFFLT_28' => 0,
                                    'XDFBOL_28' => '', /*empty*/
                                    'XDFDTE_28' => null,
                                    'XDFTXT_28' => '', /*empty*/
                                    'FILLER_28' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => null,
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => null,
                                    'BOKDTE_28' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'DBKDTE_28' => null,
                                    'REVLEV_28' => '', /*empty*/
                                    'MANPRC_28' => 'N',
                                    'ORGPRC_28' => $item->price,
                                    'PRCALC_28' => 2,
                                    'CLASS_28' => '', /*empty*/
                                    'WARRES_28' => 0,
                                    'JOB_28' => '', /*empty*/
                                    'CSENDDTE_28' => null,
                                    'CONSGND_28' => 0,
                                    'CURCONSGND_28' => 0,
                                    'CONSIGNSTK_28' => '', /*empty*/
                                    'CURSHP_28' => 0,
                                ]);

                            if ($item->art || $item->art2 || $item->customer_product_code || $item->brand) {
                                DB::connection('MAX')
                                    ->table('SO_Detail_Ext')
                                    ->updateOrInsert([
                                        'ORDER_LIN_DEL' => $max_order_num . $linum . '01',
                                    ], [
                                        'ARTE' => $item->art2 ? implode(',', [$item->art, $item->art2]) : $item->art,
                                        'CodProdCliente' => $item->customer_product_code,
                                        'Marca' => $item->brand,
                                    ]);
                            }

                            DB::connection('MAX')
                                ->table('Order_Master')
                                ->insert([
                                    'ORDNUM_10' => $max_order_num,
                                    'LINNUM_10' => $linum,
                                    'DELNUM_10' => '01',
                                    'PRTNUM_10' => $item->product,
                                    'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'RECFLG_10' => 'N',
                                    'TAXABLE_10' => 'N',
                                    'TYPE_10' => 'CU',
                                    'ORDER_10' => $max_order_num . $linum . '01',
                                    'VENID_10' => '',  /*empty*/
                                    'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'PURUOM_10' => '',  /*empty*/
                                    'CURQTY_10' => $item->quantity,
                                    'ORGQTY_10' => $item->quantity,
                                    'DUEQTY_10' => $item->quantity,
                                    'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL03_10' => '', /*empty*/
                                    'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                                    'FILL04_10' => '', /*empty*/
                                    'FRMPLN_10' => 'Y',
                                    'STATUS_10' => '3',
                                    'STK_10' => $prtnum->DELSTK_01,
                                    'CUSORD_10' => $max_order_num . $linum . '01',
                                    'PLANID_10' => $prtnum->PLANID_01,
                                    'BUYER_10' => $prtnum->BUYER_01,
                                    'PSCRAP_10' => 0,
                                    'ASCRAP_10' => 0,
                                    'SCRPCD_10' => 'N',
                                    'SCHCDE_10' => 'B',
                                    'REVLEV_10' => '', /*empty*/
                                    'COST_10' => $prtnum->COST_01,
                                    'CSTCNV_10' => 1,
                                    'APRDBY_10' => '', /*empty*/
                                    'ORDREF_10' => $max_order_num . $linum . '01',
                                    'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'FILL05_10' => '', /*empty*/
                                    'SCHFLG_10' => 'R',
                                    'CRTRAT_10' => '', /*empty*/
                                    'NEGATV_10' => '', /*empty*/
                                    'REQPEG_10' => '', /*empty*/
                                    'MPNNUM_10' => '', /*empty*/
                                    'LABOR_10' => 0,
                                    'AMMEND_10' => 'N',
                                    'LOTNUM_10' => '', /*empty*/
                                    'BEGSER_10' => '', /*empty*/
                                    'REWORK_10' => 'N',
                                    'CRTSNS_10' => 'N',
                                    'TTLSNS_10' => 0,
                                    'FORCUR_10' => 0,
                                    'EXCESS_10' => 0,
                                    'UOMCST_10' => 0,
                                    'UOMCNV_10' => 0,
                                    'INSREQ_10' => '', /*empty*/
                                    'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'RTEREV_10' => '', /*empty*/
                                    'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'COMCDE_10' => '', /*empty*/
                                    'ORDPTP_10' => '', /*empty*/
                                    'JOBEXP_10' => '', /*empty*/
                                    'JOBCST_10' => 0,
                                    'TAXCDE_10' => '', /*empty*/
                                    'TAX1_10' => 0,
                                    'GLREF_10' => '', /*empty*/
                                    'CURR_10' => '', /*empty*/
                                    'UDFKEY_10' => '', /*empty*/
                                    'UDFREF_10' => '', /*empty*/
                                    'DISC_10' => 0,
                                    'RECCOST_10' => 0,
                                    'MPNMFG_10' => '', /*empty*/
                                    'DEXPFLG_10' => 'N',
                                    'PLSTPRNT_10' => 'N',
                                    'ROUTPRNT_10' => 'N',
                                    'REQUES_10' => '', /*empty*/
                                    'CLSDTE_10' => null,
                                    'XDFINT_10' => 0,
                                    'XDFFLT_10' => 0,
                                    'XDFBOL_10' => '', /*empty*/
                                    'XDFDTE_10' => null,
                                    'XDFTXT_10' => '', /*empty*/
                                    'FILLER_10' => '', /*empty*/
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'TSKCDE_10' => '', /*empty*/
                                    'TSKTYP_10' => '', /*empty*/
                                    'REPORTER_10' => '', /*empty*/
                                    'PRIORITY_10' => '', /*empty*/
                                    'PHONE_10' => '', /*empty*/
                                    'LOCATION_10' => '', /*empty*/
                                    'ALTBOM_10' => '', /*empty*/
                                    'ALTRTG_10' => '', /*empty*/
                                    'CLASS_10' => '', /*empty*/
                                    'JOB_10' => '', /*empty*/
                                    'SUBSHP_10' => 0,
                                ]);

                            $qtycom = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('QTYCOM_29')->first();

                            DB::connection('MAX')
                                ->table('Part_Sales')
                                ->where('PRTNUM_29', '=', $item->product)
                                ->update([
                                    'QTYCOM_29' => $qtycom + floatval($item->quantity),
                                ]);

                            if ($item->notes) {
                                if (strlen($item->notes) <= 50) {
                                    DB::connection('MAX')
                                        ->table('SO_Note')
                                        ->insert([
                                            'ORDNUM_30' => $max_order_num,
                                            'LINNUM_30' => $linum,
                                            'DELNUM_30' => '01',
                                            'COMNUM_30' => '01',
                                            'CODE_30' => 'B',
                                            'COMNT_30' => $item->notes,
                                            'CUSTID_30' => '',
                                            'PIDCOD_30' => '',
                                            'MCOMP_30' => '',
                                            'MSITE_30' => '',
                                            'UDFKEY_30' => '',
                                            'UDFREF_30' => '',
                                            'XDFINT_30' => 0,
                                            'XDFFLT_30' => 0,
                                            'XDFBOL_30' => '',
                                            'XDFDTE_30' => null,
                                            'XDFTXT_30' => '',
                                            'FILLER_30' => '',
                                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                            'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                            'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                            'RECTYP_30' => 'ST',
                                        ]);
                                } else {
                                    $notes = str_split($item->notes, 50);

                                    foreach ($notes as $key => $note) {
                                        $delnum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);

                                        DB::connection('MAX')
                                            ->table('SO_Note')
                                            ->insert([
                                                'ORDNUM_30' => $max_order_num,
                                                'LINNUM_30' => $linum,
                                                'DELNUM_30' => '01',
                                                'COMNUM_30' => $delnum,
                                                'CODE_30' => 'B',
                                                'COMNT_30' => $note,
                                                'CUSTID_30' => '',
                                                'PIDCOD_30' => '',
                                                'MCOMP_30' => '',
                                                'MSITE_30' => '',
                                                'UDFKEY_30' => '',
                                                'UDFREF_30' => '',
                                                'XDFINT_30' => 0,
                                                'XDFFLT_30' => 0,
                                                'XDFBOL_30' => '',
                                                'XDFDTE_30' => null,
                                                'XDFTXT_30' => '',
                                                'FILLER_30' => '',
                                                'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                                'RECTYP_30' => 'ST',
                                            ]);
                                    }
                                }
                            }

                            DB::connection('MAX')
                                ->table('Requirement_detail')
                                ->insert([
                                    'ORDER_11' => $max_order_num . $linum . '01',
                                    'PRTNUM_11' => $item->product,
                                    'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\T00:00:00.000'),
                                    'FILL01_11' => '',
                                    'TYPE_11' => 'CU',
                                    'ORDNUM_11' => $max_order_num,
                                    'LINNUM_11' => $linum,
                                    'DELNUM_11' => '01',
                                    'CURQTY_11' => $item->quantity,
                                    'ORGQTY_11' => $item->quantity,
                                    'DUEQTY_11' => $item->quantity,
                                    'STATUS_11' => '3',
                                    'QTYPER_11' => '1',
                                    'LTOSET_11' => '0',
                                    'SCRAP_11' => '0',
                                    'PICLIN_11' => '0',
                                    'ISSQTY_11' => '0',
                                    'REQREF_11' => $max_order_num . $linum . '01',
                                    'ORDPEG_11' => '',
                                    'ASCRAP_11' => '0',
                                    'MCOMP_11' => '',
                                    'MSITE_11' => '',
                                    'UDFKEY_11' => '',
                                    'UDFREF_11' => '',
                                    'DEXPFLG_11' => '',
                                    'XDFINT_11' => '0',
                                    'XDFFLT_11' => '0',
                                    'XDFBOL_11' => '',
                                    'XDFDTE_11' => null,
                                    'XDFTXT_11' => '',
                                    'FILLER_11' => '',
                                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                ]);
                        }
                        $order->log()->create([
                            'description' => 'Pedido finalizado, descripcion: ' . $request->justify,
                            'type' => 'work_center',
                            'work_center' => $request->state === '4' ? 'dies' : ($request->state === '5' ? 'production' : 'dies'),
                            'created_by' => auth()->user()->id,
                        ]);

                        $order->order_max = $max_order_num;
                        $order->state = '10';
                        $order->save();

                        DB::commit();
                        DB::connection('MAX')->commit();

                        $orders = DB::table('order_headers')
                            ->where('state', '=', $request->state)
                            ->where('substate', '=', 'P')
                            ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        $finished = DB::table('order_headers')
                            ->where('state', '=', '10')
                            ->orderBy('consecutive', 'desc')
                            ->get();

                        return response()->json([
                            'orders' => $orders,
                            'finished' => $finished,
                            'order_generated' => $max_order_num,
                        ], 200);
                    default:
                        return response()->json('error order type', 500);
                }
            } else {
                throw new Exception('unprocessable entity', 422);
            }
        } catch (Exception $e) {
            DB::rollBack();
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $days
     * @return object
     */
    private function calculate_delivery($days): object
    {
        $business_days = DB::connection('MAX')
            ->table('Shop_Calendar')
            ->where('ShopDay', '=', 1)
            ->whereDate('DateValue', '>=', Carbon::now())
            ->get();

        if ($days > 0) {
            return $business_days[$days - 1];
        } else {
            $date = Carbon::now()->format('Y-m-d h:m:i');

            return (object)[
                'DateValue' => $date,
            ];
        }
    }

    /**
     * edit
     *
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $order = HeaderOrder::with('customer', 'details.product_info', 'seller')->find($id);

        if ($order->state == '0' || $order->state == '1' || $order->state == '7' || $order->state == '2' && $order->substate == 'R' || $order->state == '3' && $order->substate == 'R') {
            return Inertia::render('Applications/Orders/Edit', [
                'order' => $order,
            ]);
        } else {
            abort(202, 'Unauthorized action.');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function mark_order(Request $request): JsonResponse
    {
        try {
            HeaderOrder::find($request->id)
                ->update([
                    'mark' => $request->icon,
                ]);

            $orders = DB::table('order_headers')
                ->where('state', '=', '5')
                ->where('substate', '=', 'P')
                ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                ->orderBy('consecutive', 'desc')
                ->get();

            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function customer_product_prices(): Response
    {
        return Inertia::render('Applications/Orders/CustomerProductPrice');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_product_prices_get_info(Request $request): JsonResponse
    {
        $data = PriceItemOrder::with('product_info', 'customer', 'approved')
            ->where('customer_code', '=', $request->code)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function customer_product_prices_update(Request $request, $id): JsonResponse
    {
        try {
            PriceItemOrder::find($id)->update([
                'price' => $request->price,
                'customer_product_code' => $request->customer_product_code,
                'notes' => $request->notes,
                'state' => $request->state,
            ]);

            $data = PriceItemOrder::with('product_info', 'customer', 'approved')
                ->where('customer_code', '=', $request->customer_code)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function production_send_wallet(Request $request): JsonResponse
    {
        try {
            $order = HeaderOrder::find($request->id);

            $order->update([
                'state' => '4',
                'substate' => 'P',
                'destiny' => 'C',
            ]);

            $order->log()->create([
                'description' => 'Pedido enviado a bodega, descripcion: ' . $request->justify,
                'type' => 'user',
                'work_center' => 'production',
                'created_by' => auth()->user()->id,
            ]);

            $orders = DB::table('order_headers')
                ->where('state', '=', '5')
                ->where('substate', '=', 'P')
                ->orderByRaw("CASE
                                     WHEN type = 'samples' THEN 1
                                     WHEN type = 'national' THEN 2
                                     WHEN type = 'export' THEN 3
                                     WHEN type = 'nationalUSD' THEN 4
                                     WHEN type = 'delivered_merchandise' THEN 5
                                     WHEN type = 'claim' THEN 6
                                     WHEN type = 'recycling' THEN 7
                                     ELSE 8
                                   END")
                ->orderBy('consecutive', 'desc')
                ->get();

            $finished = DB::table('order_headers')
                ->where('state', '=', '10')
                ->orderBy('consecutive', 'desc')
                ->get();

            return response()->json([
                'orders' => $orders,
                'finished' => $finished,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws BindingResolutionException
     * @throws MpdfException
     * @throws BindingResolutionException
     */
    public function view_pdf($id): \Illuminate\Http\Response
    {
        $order = HeaderOrder::with('details', 'seller', 'customer')->find($id);
        $order->append('retention');

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.orders.header', compact('order')));
        $pdf->SetHTMLFooter(View::make('pdfs.orders.footer', compact('order')));
        $pdf->WriteHTML(View::make('pdfs.orders.template', compact('order')), HTMLParserMode::HTML_BODY);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
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

    /**
     * @param $oc
     * @param $customer
     * @return JsonResponse
     */
    public function validate_oc($oc, $customer): JsonResponse
    {
        $count = HeaderOrder::where('customer_code', '=', $customer)
            ->where('oc', '=', $oc)
            ->count();

        $count = $count > 0;

        return response()->json($count, 200);
    }

    /**
     * @param $code
     * @return mixed
     */
    public function suggested_products($code)
    {
        return DetailOrder::whereHas('header', function ($q) use ($code) {
            $q->where('customer_code', '=', $code);
        })->where('created_at', '>', Carbon::now()->subYear())
            ->orderBy('product', 'asc')
            ->take(20)
            ->get()
            ->map(function ($row) {
                return [
                    'product' => $row->product,
                    'description_product' => $row->description,
                    'stock' => 0,
                    'destiny' => $row->destiny,
                    'type' => $row->type,
                    'unit_measurement' => $row->unit_measurement,
                    'price' => (float)$row->price,
                    'quantity' => (float)$row->quantity,
                    'art' => $row->art,
                    'art2' => '',
                    'brand' => $row->brand,
                    'notes' => '',
                    'customer_product_code' => $row->customer_product_code ?? '',
                ];
            });
    }

    /**
     * @param $array
     * @return bool
     */
    protected function check_product($array): bool
    {
        if (count($array) > 0) {
            foreach ($array as $item) {
                $result = DB::connection('MAX')
                    ->table('Part_Master')
                    ->where('PRTNUM_01', '=', $item->product)
                    ->count();

                if ($result === 0) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
