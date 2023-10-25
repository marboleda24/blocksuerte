<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PendingController extends Controller
{

    /**
     * @return \Inertia\Response
     */
    public function dataop()
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_EstadoOP_v1')
            ->where('EstadoOP', '<', 4)
            ->get();
        return Inertia::render('Applications/Pendings/Dataop', [
            'op' => $data]);

    }

    /**
     * @return \Inertia\Response
     */
    public function ovopen()
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_OVAbiertas')
            ->get()->map(function ($row) {
                return [
                    'OV' => $row->OV . $row->LINEA . $row->ITEM,
                    'OC' => $row->OC,
                    'FECHA_OV' => Carbon::parse($row->FECHA_OV)->format('Y-m-d'),
                    'CANT_ACTUAL' => $row->CANT_ACTUAL,
                    'RAZON_SOCIAL' => $row->RAZON_SOCIAL,
                    'PRODUCTO' => $row->PRODUCTO,
                    'NOMVENDEDOR' => $row->NOMVENDEDOR,
                    'ESTADO' => $row->ESTADO,
                    'NOTAS' => $row->NOTAS,
                    'ARTE' => $row->ARTE,
                    'Marca' => $row->Marca,
                    'DIAS_DIFERENCIA' => Carbon::now()->diffInDays($row->FECHA_OV),
                    'centro de trabajo ' => $row->OC <> null ? 'hay horden de producion' : 'no hay orden ',
                    'URGENCIA' => Carbon::now()->diffInDays($row->FechaCompromiso),

                ];
            });
        return Inertia::render('Applications/Pendings/OvOpen', [
            'ov' => $data]);

    }

    /**
     * @return \Inertia\Response
     */
    public function dataov()
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_OVAbiertas')
            ->orderBy('FECHA_OV', 'desc')
            ->get()->map(function ($row) {
                return [
                    'AÑO' => Carbon::parse($row->FECHA_OV)->format('Y'),
                    'MES' => Carbon::parse($row->FECHA_OV)->format('m'),
                    'NOMVENDEDOR' => $row->NOMVENDEDOR,
                    'RAZON_SOCIAL' => $row->RAZON_SOCIAL,
                    'OV' => $row->OV
                ];
            });


        return Inertia::render('Applications/Pendings/Dateov', [
            'op' => $data]);


    }

    /**
     * @return \Inertia\Response
     */
    public function hot()
    {

        $OV = DB::connection('MAX')
            ->table('CIEV_V_OVAbiertas')
            ->where('ESTADO', '<', 4)
            ->get();
        $data = DB::connection('MAX')
            ->table('CIEV_V_EstadoOP_v1')
            ->where('EstadoOP', '<', 4)
            ->get()->map(function ($row) {
                return [
                    'ORDEN _CT' => $row->CT,
                    'CT' => $row->CT,
                    'fecha' => Carbon::now()->diffInDays($row->CREDTE_10) / 7
                ];
            });

        return Inertia::render('Applications/Pendings/Hot', [
            'op' => $data,
            'ov' => $OV]);

    }


    /**
     * @return \Inertia\Response
     */
    public function summary()
    {
        $result = DB::connection('MAX')
            ->table('CIEV_V_EstadoOP_v1')
            ->leftJoin('CIEV_V_OVAbiertas', function ($join) {
                $join->on('CIEV_V_EstadoOP_v1.OV', 'like',
                    DB::raw("CONCAT(CIEV_V_OVAbiertas.OV, LINEA, ITEM)"));
            })
            ->select('CIEV_V_EstadoOP_v1.*', 'CIEV_V_OVAbiertas.*')
            ->get();

        $OV = DB::connection('MAX')
            ->table('CIEV_V_OVAbiertas')
            ->where('ESTADO', '<', 4)
            ->get()->map(function ($row) {
                return [
                    'OV' => $row->OV,
                    'VENDEDOR' => $row->NOMVENDEDOR,
                    'CANTIDAD' => $row->CANT_PENDIENTE * $row->PRECIO,

                ];

            })
            ->groupBy('VENDEDOR')
            ->map(function ($groupedItems) {
                return [
                    'TOTAL' => round($groupedItems->sum('CANTIDAD')),
                ];
            });
        return Inertia::render('Applications/Pendings/Summary', [
            'OV' => $OV,
            'op' => $result]);
    }

    /**
     * @return \Inertia\Response
     */
    public function samples()
    {
        $array = [];

        $query = DB::connection('MAX')->table('CIEV_V_OVAbiertas')
            ->where('ESTADO', '<', 4)
            ->get()
            ->map(function ($row) {
                return [
                    'weeks' => Carbon::now()->diffInWeeks($row->FECHA_OV),
                    'vendor' => trim($row->NOMVENDEDOR)
                ];
            });

        $query = $query->groupBy('vendor');

        foreach ($query as $key => $item) {
            $groupByWeeks = $item->groupBy('weeks');

            $array[$key] = [
                'weeks' => [],
                'vendedor' => $key
            ];

            foreach ($groupByWeeks as $index => $week) {
                if ($index > 0 && $index <= 30) {
                    $array[$key]['weeks'][$index][] = $week->count();
                }
            }
            sort($array);
        }

        $finalTable = [];
        $flag = 0;
        rsort($finalTable);

        foreach ($array as $item) {
            $finalTable[$flag]['vendedor'] = $item['vendedor'];
            foreach ($item['weeks'] as $idx => $value) {
                for ($i = 1; $i <= 30; $i++) {
                    if (intval($idx) === $i) {
                        $finalTable[$flag][$i] = $value[0];
                    } else if (!array_key_exists($i, $finalTable[$flag])) {
                        $finalTable[$flag][$i] = 0;
                    }
                }
            }
            $flag++;

        }


        $columnas = array_keys($finalTable[0]);
        $columnasEliminar = array();

        foreach ($columnas as $columna) {
            $valoresColumna = array_column($finalTable, $columna);
            if (array_sum($valoresColumna) == 0) {
                $columnasEliminar[] = $columna;
            }
        }

        foreach ($finalTable as &$fila) {
            foreach ($columnasEliminar as $columnaEliminar) {
                if ($columnaEliminar === 'vendedor') {
                    continue;
                }
                unset($fila[$columnaEliminar]);
            }

        }
        return Inertia::render('Applications/Pendings/Summary', [
            'OV' => $finalTable
        ]);
    }
}
