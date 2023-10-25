<?php

namespace Database\Seeders;

use App\Models\ClaimWorkplace;
use Illuminate\Database\Seeder;

class ClaimWorkplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workplaces = [
            'Comercial',
            'Compras',
            'Despachos',
            'Diseño',
            'Cartera',
            'Contabilidad',
            'Costos',
            'Sistemas',
            'Tesorería',
            'Externo',
            'Facturación',
            'Gestión calidad',
            'Inspección y empaque',
            'Inventario',
            'Centrifugado',
            'Grabado',
            'Diseño y desarrollo',
            'Electro erosionado',
            'Encabezado',
            'Ensamble',
            'Galvanoplastia',
            'Inyección',
            'Pintura',
            'Taller de fabricación',
            'Troquelado',
        ];

        collect($workplaces)->each(function ($workplace) {
            $w = new ClaimWorkplace();
            $w->name = $workplace;
            $w->save();
        });
    }
}
