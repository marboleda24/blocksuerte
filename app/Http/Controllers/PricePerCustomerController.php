<?php

namespace App\Http\Controllers;

use App\Exports\PricePerCustomerExport;
use App\Models\PriceItemOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Runner\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PricePerCustomerController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/PricePerCustomer');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_data(Request $request)
    {
        try {
            $data = PriceItemOrder::with('product_info')
                ->select('customer_code', 'product', 'customer_product_code', 'price')
                ->where('customer_code', '=', $request->code)
                ->whereNotNull('customer_product_code')
                ->where('price', '>', 0)
                ->distinct()
                ->get()->map(function ($row){
                    return (object)[
                        'product' => join(' – ', [$row->product, $row->product_info?->Descripcion]),
                        'customer_product_code' => $row->customer_product_code,
                        'customer_code' => $row->customer_code,
                        'price' => $row->price,
                        'new_price' => 0,
                    ];
                });

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request){
        try {
            return Excel::download(new PricePerCustomerExport($request->selected), 'file.xlsx');
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
}
