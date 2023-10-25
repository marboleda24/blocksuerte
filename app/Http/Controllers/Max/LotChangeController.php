<?php

namespace App\Http\Controllers\Max;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class LotChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:max.lot-change');
    }

    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/MaxUpdate/LotChange');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function replace_data(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Lot_Tracking_Header')
                ->where('ORDNUM_70', '=', "{$request->production_order}0000")
                ->update([
                    'LOTNUM_70' => DB::raw("REPLACE(LOTNUM_70, '{$request->old_value}', '{$request->new_value}')"),
                ]);

            DB::connection('MAX')
                ->table('Lot_Tracking_Hist')
                ->where('ORDNUM_72', '=', "{$request->production_order}0000")
                ->update([
                    'LOTNUM_72' => DB::raw("REPLACE(LOTNUM_72, '{$request->old_value}', '{$request->new_value}')"),
                ]);

            DB::connection('MAX')
                ->table('Order_Master')
                ->where('ORDNUM_10', '=', $request->production_order)
                ->update([
                    'LOTNUM_10' => DB::raw("REPLACE(LOTNUM_10, '{$request->old_value}', '{$request->new_value}')"),
                ]);

            DB::connection('MAX')->commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
