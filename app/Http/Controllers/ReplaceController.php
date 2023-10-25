<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ReplaceController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.replace-data');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/Replace/Index');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');

            $part_master = DB::connection('MAX')
                ->table('Part_Master')
                ->where('STAENG_01', '=', '2')
                ->where('PMDES1_01', 'LIKE', "$search")
                ->orWhere('PRTNUM_01', 'LIKE', "$search")
                ->orderBy('PMDES1_01')
                ->get()->map(function ($row) {
                    return [
                        'code' => trim($row->PRTNUM_01),
                        'description' => trim($row->PMDES1_01),
                        'new_description' => '',
                    ];
                });

            $part_sales = DB::connection('MAX')
                ->table('Part_Sales')
                ->where('PMDES1_29', 'LIKE', "$search")
                ->orWhere('PRTNUM_29', 'LIKE', "$search")
                ->orderBy('PMDES1_29')
                ->get()->map(function ($row) {
                    return [
                        'code' => trim($row->PRTNUM_29),
                        'description' => trim($row->PMDES1_29),
                        'new_description' => '',
                    ];
                });

            $source_materials = DB::connection('VISUAL_CONTROL')
                ->table('MateriasPrimas')
                ->where('Descripcion', 'LIKE', "$search")
                ->orWhere('Codigo', 'LIKE', "$search")
                ->orderBy('Descripcion')
                ->get()->map(function ($row) {
                    return [
                        'code' => $row->Codigo,
                        'description' => $row->Descripcion,
                        'new_description' => '',
                    ];
                });

            $products = DB::connection('VISUAL_CONTROL')
                ->table('Productos')
                ->where('Descripcion', 'LIKE', "$search")
                ->orWhere('Codigo', 'LIKE', "$search")
                ->orderBy('Descripcion')
                ->get()->map(function ($row) {
                    return [
                        'code' => $row->Codigo,
                        'description' => $row->Descripcion,
                        'new_description' => '',
                    ];
                });

            return response()->json([
                'part_master' => $part_master,
                'part_sales' => $part_sales,
                'source_materials' => $source_materials,
                'products' => $products,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function replace_data(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('VISUAL_CONTROL')->beginTransaction();
        try {
            foreach ($request->part_master as $row) {
                if ($row['description'] != $row['new_description']) {
                    DB::connection('MAX')
                        ->table('Part_Master')
                        ->where('PRTNUM_01', '=', $row['code'])
                        ->where('PMDES1_01', '=', $row['description'])
                        ->update([
                            'PMDES1_01' => $row['new_description'],
                        ]);
                }
            }

            foreach ($request->part_sales as $row) {
                if ($row['description'] != $row['new_description']) {
                    DB::connection('MAX')
                        ->table('Part_Sales')
                        ->where('PRTNUM_29', '=', $row['code'])
                        ->where('PMDES1_29', '=', $row['description'])
                        ->update([
                            'PMDES1_29' => $row['new_description'],
                        ]);
                }
            }

            foreach ($request->products as $row) {
                if ($row['description'] != $row['new_description']) {
                    DB::connection('VISUAL_CONTROL')
                        ->table('Productos')
                        ->where('Codigo', '=', $row['code'])
                        ->where('Descripcion', '=', $row['description'])
                        ->update([
                            'Descripcion' => $row['new_description'],
                        ]);
                }
            }

            foreach ($request->source_materials as $row) {
                if ($row['description'] != $row['new_description']) {
                    DB::connection('VISUAL_CONTROL')
                        ->table('MateriasPrimas')
                        ->Where('Codigo', '=', $row['code'])
                        ->where('Descripcion', '=', $row['description'])
                        ->update([
                            'Descripcion' => $row['new_description'],
                        ]);
                }
            }
            DB::connection('MAX')->commit();
            DB::connection('VISUAL_CONTROL')->commit();

            return response()->json('Informacion actualizada correctamente', 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('VISUAL_CONTROL')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
