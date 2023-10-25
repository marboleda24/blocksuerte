<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ProductTrait
{
    /**
     * @param  string  $product
     * @param  string  $customer_code
     * @return array
     */
    protected function average_product(string $product, string $customer_code): array
    {
        $average_price = DB::connection('MAX')
            ->table('CIEV_V_FacturasDetalladas')
            ->where('CodigoCliente', '=', $customer_code)
            ->where('CodigoProducto', '=', $product)
            ->orderBy('Factura', 'desc')
            ->take(5)->get();

        if ($average_price) {
            $average_price = $average_price->sum('Precio') / 5;
            $average_price = number_format((float) $average_price, 3);
            $max_price = number_format($average_price + ($average_price * 0.05), 3);
            $min_price = number_format($average_price - ($average_price * 0.05), 3);

            return [
                'average_price' => $average_price,
                'max_price' => $max_price,
                'min_price' => $min_price,
            ];
        } else {
            return [
                'average_price' => 0.00,
                'max_price' => 0.00,
                'min_price' => 0.00,
            ];
        }
    }
}
