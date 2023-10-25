<?php

namespace Database\Seeders;

use App\Models\ClaimReprocessingReason;
use Illuminate\Database\Seeder;

class ClaimReprocessingReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reasons = [
            [
                'name' => 'Arte malo o problemas en diseño gráfico',
                'code' => 'N1',
            ],
            [
                'name' => 'Despacho incorrecto o con problemas',
                'code' => 'N1',
            ],
            [
                'name' => 'Ensamble incorrecto',
                'code' => 'N1',
            ],
            [
                'name' => 'Factura incorrecta',
                'code' => 'N5',
            ],
            [
                'name' => 'Incumplimiento en tiempos de entrega',
                'code' => 'N1',
            ],
            [
                'name' => 'Mal almacenamiento o manejo por parte del cliente',
                'code' => 'N1',
            ],
            [
                'name' => 'Mal atención',
                'code' => 'N1',
            ],
            [
                'name' => 'Mal programado de parte del cliente',
                'code' => 'N1',
            ],
            [
                'name' => 'Pedido mal etiquetado',
                'code' => 'N1',
            ], [
                'name' => 'Pedido mal programado desde ventas',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto en uso se daño o no funciona',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto mal revisado',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto parcialmente deteriorado',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto que no corresponde a las características solicitadas',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto que no pega',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto que se daña al aplicarse',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto totalmente deteriorado',
                'code' => 'N1',
            ],
            [
                'name' => 'Terminado',
                'code' => 'N1',
            ],
            [
                'name' => 'Color o tono incorrecto o con problemas',
                'code' => 'N1',
            ],
            [
                'name' => 'Cantidad errada',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente devuelve por fecha',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente devuelve sin justificación valida',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente no puede pagar mercancía',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente no solicito mercancía',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente pide mercancía equivocada',
                'code' => 'N1',
            ],
            [
                'name' => 'Cliente reemplaza mercancía',
                'code' => 'N1',
            ],
            [
                'name' => 'Descuento o iva no aplicado',
                'code' => 'N6',
            ],
            [
                'name' => 'Despacho o pedido doble',
                'code' => 'N1',
            ],
            [
                'name' => 'Devolución por inventario',
                'code' => 'N1',
            ],
            [
                'name' => 'El cliente cancelo el pedido y no se informo ',
                'code' => 'N1',
            ],
            [
                'name' => 'En facturación',
                'code' => 'N5',
            ],
            [
                'name' => 'El cliente no contabilizo la factura',
                'code' => 'N2',
            ],
            [
                'name' => 'Error del sistema',
                'code' => 'N6',
            ],
            [
                'name' => 'Error en el pedido',
                'code' => 'N6',
            ],
            [
                'name' => 'Error en el precio',
                'code' => 'N5',
            ],
            [
                'name' => 'Error en facturación',
                'code' => 'N6',
            ],
            [
                'name' => 'Falta de información del comercial',
                'code' => 'N6',
            ],
            [
                'name' => 'Gestiones administrativas',
                'code' => 'N6',
            ],
            [
                'name' => 'La orden de compra ya esta cumplida',
                'code' => 'N1',
            ],
            [
                'name' => 'Mercancía en condición de préstamo',
                'code' => 'N1',
            ],
            [
                'name' => 'Troqueles - Maquinas',
                'code' => 'N6',
            ],
            [
                'name' => 'Mercancía en exceso',
                'code' => 'N1',
            ],
            [
                'name' => 'Problemas de calidad',
                'code' => 'N1',
            ],
            [
                'name' => 'Producto no cumple con las características exigidas por el cliente',
                'code' => 'N1',
            ],
            [
                'name' => 'Repetir factura por cambio de razón social',
                'code' => 'N5',
            ],
            [
                'name' => 'Vendedor pide mercancía que no solicito el cliente',
                'code' => 'N1',
            ],
            [
                'name' => 'Vendedor solicita producto diferente',
                'code' => 'N1',
            ],
        ];

        collect($reasons)->each(function ($reason) {
            ClaimReprocessingReason::create($reason);
        });
    }
}
