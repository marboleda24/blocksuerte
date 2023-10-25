<?php

namespace Database\Seeders;

use App\Models\FeatureUnit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FeatureUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = [
            [
                'code' => 'B',
                'name' => 'BASE',
                'comments' => 'BASE',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'A',
                'name' => 'ALTURA',
                'comments' => 'ALTURA',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'D',
                'name' => 'DIAMETRO',
                'comments' => 'DIAMETRO',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'P',
                'name' => 'PERFORACION',
                'comments' => 'PERFORACION',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'PE',
                'name' => 'PESTAÑA',
                'comments' => 'PESTAÑA',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'E',
                'name' => 'ESPESOR',
                'comments' => 'ESPESOR',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        FeatureUnit::insert($features);
    }
}
