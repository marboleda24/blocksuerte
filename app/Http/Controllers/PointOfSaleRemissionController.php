<?php

namespace App\Http\Controllers;

use App\Models\PointOfSaleRemissionDetail;
use App\Models\PointOfSaleRemissionHeader;
use App\Traits\PointOfSaleRemissionTrait;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\MpdfException;

class PointOfSaleRemissionController extends Controller
{
    use PointOfSaleRemissionTrait;

    /**
     * @return Response
     */
    public function index()
    {
        $remissions = PointOfSaleRemissionHeader::with('user', 'detail.lots', 'order')->get();

        return Inertia::render('Applications/PointOfSaleRemission/Index', [
            'remissions' => $remissions,
        ]);
    }

    /**
     * @param $consecutive
     * @return Response
     */
    public function edit($consecutive)
    {
        $remission = PointOfSaleRemissionHeader::with('user', 'detail.lots', 'order')
            ->where('consecutive', '=', $consecutive)
            ->first();

        $warehouses = DB::connection('MAXP')
            ->table('Stock_Master')
            ->select('STK_05', 'DESC_05')
            ->get();

        return Inertia::render('Applications/PointOfSaleRemission/Edit', [
            'remission' => $remission,
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->detail as $detail) {
                $dt = PointOfSaleRemissionDetail::with('lots')
                    ->find($detail->id);

                $dt->warehouse = $detail->warehouse;
                $dt->quantity = $detail->quantity;
                $dt->save();

                $dt->lots->delete();
                $dt->lots->creaMany($detail->lots);
            }

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $consecutive
     * @return JsonResponse
     */
    public function view($consecutive)
    {
        $remission = PointOfSaleRemissionHeader::with('user', 'detail.lots', 'order')
            ->where('consecutive', '=', $consecutive)
            ->first();

        return response()->json($remission, 200);
    }

    /**
     * @param $consecutive
     * @return void
     *
     * @throws BindingResolutionException
     * @throws MpdfException
     */
    public function print($consecutive)
    {
        $pdf = $this->makePdf($consecutive);

        return response()->make($pdf->Output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @t
     */
    public function check(Request $request)
    {
        return $this->check_remission($request->consecutive);
    }
}
