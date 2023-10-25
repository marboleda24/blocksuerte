<?php

namespace Database\Seeders;

use App\Models\RemissionType;
use Illuminate\Database\Seeder;

class RemissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['description' => 'Mercancía entregada'],
            ['description' => 'OV por facturar'],
            ['description' => 'Reclamo'],
            ['description' => 'Envío punto de venta'],
            ['description' => 'Salida de activos'],
            ['description' => 'Alquiler troqueles'],
        ];

        collect($types)->each(function ($type) {
            RemissionType::create($type);
        });
    }
}
