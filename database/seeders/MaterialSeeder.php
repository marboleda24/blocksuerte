<?php

namespace Database\Seeders;

use App\Models\MaterialExt;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materials = [
            [
                'code' => 'A',
                'name' => 'ALUMINIO',
                'abbreviation' => 'ALU',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'C',
                'name' => 'COBRE',
                'abbreviation' => 'COB',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'E',
                'name' => 'PAPEL LITOGRAFICO',
                'abbreviation' => 'PAL',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'H',
                'name' => 'HIERRO',
                'abbreviation' => 'HIE',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'I',
                'name' => 'ACERO INOX',
                'abbreviation' => 'AINOX',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'L',
                'name' => 'LATON',
                'abbreviation' => 'LAT',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'P',
                'name' => 'POLIPROPILENO',
                'abbreviation' => 'PP',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => '0',
                'name' => 'N/A',
                'abbreviation' => 'N/A',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'Z',
                'name' => 'ZAMAK',
                'abbreviation' => 'ZMK',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'N',
                'name' => 'SINTETICO',
                'abbreviation' => 'SNT',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'T',
                'name' => 'TELA',
                'abbreviation' => 'TELA',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'code' => 'V',
                'name' => 'PVC',
                'abbreviation' => 'PVC',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'R',
                'name' => 'RML',
                'abbreviation' => 'REMOLIDA',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'O',
                'name' => 'ACE',
                'abbreviation' => 'ACERO',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'M',
                'name' => 'POM',
                'abbreviation' => 'POLIACETAL',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'U',
                'name' => 'CUE',
                'abbreviation' => 'CUERO',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'D',
                'name' => 'DF2',
                'abbreviation' => 'ACERO DF2',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'B',
                'name' => 'POL',
                'abbreviation' => 'POLIESTER',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'G',
                'name' => 'VID',
                'abbreviation' => 'VIDRIO',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        MaterialExt::insert($materials);
    }
}
