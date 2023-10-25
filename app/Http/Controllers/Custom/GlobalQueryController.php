<?php

namespace App\Http\Controllers\Custom;

use App\Custom\ReadXML;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GlobalQueryController extends Controller
{
    use ReadXML;

    /**
     * @return JsonResponse
     */
    public function get_countries_dms()
    {
        $countries = DB::connection('DMS')
            ->table('y_paises')
            ->orderBy('descripcion')
            ->get();

        return response()->json($countries, 200);
    }

    /**
     * @param $country
     * @return JsonResponse
     */
    public function get_departments_dms($country)
    {
        $departments = DB::connection('DMS')
            ->table('y_departamentos')
            ->where('pais', '=', $country)
            ->orderBy('descripcion')
            ->get();

        return response()->json($departments, 200);
    }

    /**
     * @param $country
     * @param $department
     * @return JsonResponse
     */
    public function get_cities_dms($country, $department)
    {
        $departments = DB::connection('DMS')
            ->table('y_ciudades')
            ->where('pais', '=', $country)
            ->where('departamento', '=', $department)
            ->orderBy('descripcion')
            ->get();

        return response()->json($departments, 200);
    }

    /**
     * @param $document
     * @return JsonResponse
     */
    public function validate_provider_dms($document)
    {
        $count = DB::connection('DMS')
            ->table('terceros')
            ->where('nit', '=', $document)
            ->count();

        if ($count > 0) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function save_provider(Request $request)
    {
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('DMS')
                ->table('terceros')
                ->insert([
                    'nit' => '',
                    'digito' => '',
                    'nombres' => '',
                    'direccion' => '',
                    'ciudad' => '',
                    'telefono_1' => '',
                    'telefono_2' => '',
                    'fax' => '',
                    'tipo_identificacion' => '',
                    'pais' => '',
                    'gran_contribuyente' => '',
                    'autoretenedor' => '',
                    'bloqueo' => '',
                    'notas' => '',
                    'concepto_1' => '',
                    'concepto_2' => '',
                    'concepto_3' => '',
                    'concepto_4' => '',
                    'concepto_5' => '',
                    'concepto_6' => '',
                    'concepto_7' => '',
                    'concepto_8' => '',
                    'concepto_9' => '',
                    'concepto_10' => '',
                    'mail' => '',
                    'regimen' => '',
                    'cupo_credito' => '',
                    'nit_real' => '',
                    'condicion' => '',
                    'vendedor' => '',
                    'fletes' => '',
                    'contacto_1' => '',
                    'y_dpto' => '',
                    'y_ciudad' => '',
                    'celular' => '',
                    'y_pais' => '',
                    'codigo_alterno' => '',
                ]);

            DB::connection('DMS')->commit();

            return response()->json('success', 200);
        } catch (\Exception $e) {
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @throws \Exception
     */
    public function test_xml_reader()
    {
        if (file_exists(storage_path().'/app/supplier_purchases/fv08605245230152200000132.xml')) {
            $result = $this->readXMLv2(storage_path().'/app/supplier_purchases/fv08605245230152200000132.xml');

            return $result;
        } else {
            throw new \Exception('el documento no exite', 500);
        }
    }
}
