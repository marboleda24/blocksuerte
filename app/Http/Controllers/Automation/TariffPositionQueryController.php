<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Controller;
use App\Models\Automation\TariffPositionQuery;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TariffPositionQueryController extends Controller
{
    public function index(): Response
    {
        $queries = TariffPositionQuery::with('createdby')->get();

        return Inertia::render('Applications/Automation/TariffPositionQuery', [
            'queries' => $queries,
        ]);
    }

    /**
     * @param  TariffPositionQuery  $tariffPositionQuery
     * @return JsonResponse
     */
    public function store(TariffPositionQuery $tariffPositionQuery): JsonResponse
    {
        $tariffPositionQuery->created_id = Auth::id();
        $tariffPositionQuery->save();

        $tpq = TariffPositionQuery::with('createdby')->get();

        return response()->json($tpq, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        TariffPositionQuery::find($id)->delete();

        $tpq = TariffPositionQuery::with('createdby')->get();

        return response()->json($tpq, 200);
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        TariffPositionQuery::find($id)
            ->update($request->all());

        $tpq = TariffPositionQuery::with('createdby')->get();

        return response()->json($tpq, 200);
    }
}
