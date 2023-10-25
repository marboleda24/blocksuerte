<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ReasonValidationController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $data = DB::connection('MAX')
            ->table('CIEV_V_REASON_VALIDATION')
            ->get();

        $reasons = DB::connection('MAX')
            ->table('Code_Master')
            ->where('CDEKEY_36', '=', 'REAS')
            ->orderBy('DESC_36')
            ->get();

        return Inertia::render('Applications/ReasonValidation', [
            'data' => $data,
            'reasons' => $reasons
        ]);
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
            DB::connection('MAX')
                ->table('SO_Master')
                ->where('ORDNUM_27', '=', $request->ov)
                ->update([
                    'REASON_27' => $request->reason
                ]);

            DB::connection('MAX')
                ->table('Invoice_Master')
                ->where('ORDNUM_31', '=', $request->ov)
                ->update([
                    'REASON_31' => $request->reason
                ]);

            DB::connection('MAX')->commit();

            $data = DB::connection('MAX')
                ->table('CIEV_V_REASON_VALIDATION')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}
