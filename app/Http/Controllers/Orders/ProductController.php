<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function average_product(string $product): JsonResponse
    {
        $average_price = DB::connection('MAX')
            ->table('CIEV_V_FacturasDetalladas')
            ->where('CodigoProducto', '=', $product)
            ->orderBy('Factura', 'desc')
            ->take(5)->get();

        $average_price = $average_price->sum('Precio') / 5;
        $average_price = number_format((float) $average_price, 3);
        $max_price = number_format($average_price + ($average_price * 0.05), 3);
        $min_price = number_format($average_price - ($average_price * 0.05), 3);

        $result = [
            'average_price' => $average_price,
            'max_price' => $max_price,
            'min_price' => $min_price,
        ];

        return response()->json($result);
    }
}
