<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class UpdateStructureController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/UpdateStructure/UpdateStructure');
    }

    /**
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function search(Request $request, string $type = 'product')
    {
        try {
            $result = DB::connection('MAX')
                ->table('CIEV_V_DNL_PRODUCTS')
                ->where($type === 'product' ? 'PARENT' : 'COMPONENT', 'LIKE', "%{$request->q}%")
                ->orderBy('PARENT')
                ->get();

            $collection = $type === 'product'
                ? $result->groupBy('PARENT')
                : $result->unique('COMPONENT')->values()->all();

            return response()->json($collection, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            foreach ($request->selected as $product) {
                foreach ($request->components as $component) {
                    if ($component['type'] === 'remove') {
                        DB::connection('MAX')
                            ->table('Product_Structure')
                            ->where('PARPRT_02', '=', $product)
                            ->where('COMPRT_02', '=', $component['component']['COMPONENT'])
                            ->update([
                                'QTYPER_02' => 0,
                                'ModifiedBy' => auth()->user()->username . "-EVPIU",
                                'ModificationDate' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                            ]);
                    } else if ($component['type'] === 'add') {
                        DB::connection('MAX')
                            ->table('Product_Structure')
                            ->insert([
                                "PARPRT_02" => $product,
                                "COMPRT_02" => $component['component']['COMPONENT'],
                                "EFFDTE_02" => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                "FILL01_02" => "",
                                "QTYPER_02" => $component['quantity'],
                                "QTYCDE_02" => "U",
                                "LTOSET_02" => "0",
                                "TYPCDE_02" => "N",
                                "SCRAP_02" => "0",
                                "ECN_02" => "",
                                "ACTDTE_02" => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                "FILL02_02" => "",
                                "ALTPRT_02" => "",
                                "REFDES_02" => "",
                                "MPNSTR_02" => "N",
                                "MCOMP_02" => "",
                                "MSITE_02" => "",
                                "UDFKEY_02" => "",
                                "UDFREF_02" => "",
                                "XDFINT_02" => "0",
                                "XDFFLT_02" => "0",
                                "XDFBOL_02" => "",
                                "XDFDTE_02" => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                "XDFTXT_02" => "",
                                "FILLER_02" => "",
                                "CreatedBy" => auth()->user()->username . "-EVPIU",
                                "CreationDate" => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                "ModifiedBy" => auth()->user()->username . "-EVPIU",
                                "ModificationDate" => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                "ALTCDE_02" => "",
                            ]);
                    }else {
                        throw new Exception("Transaccion Invalida", 500);
                    }
                }
            }

            DB::connection('MAX')->commit();
            return response()->json('success', 200);
        }catch (Exception $e){
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}
