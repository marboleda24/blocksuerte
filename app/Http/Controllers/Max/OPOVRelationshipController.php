<?php

namespace App\Http\Controllers\Max;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OPOVRelationshipController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Applications/OPOVRelationship');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $query = DB::table('V_OP_OV_RELATIONSHIP')
            ->whereBetween('ov', [$request->start, $request->end])
            ->orderBy('ov')
            ->orderBy('item')
            ->get()->map(function ($row) {
                $row->op = '';
                return $row;
            });

        $query = $query->groupBy('ov');

        return response()->json($query, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->toArray() as $group) {
                foreach ($group['items'] as $value) {
                    DB::table('op_ov_relationship')
                        ->updateOrInsert([
                            'ov' => $value['ov'],
                            'item' => $value['item']
                        ], [
                            'op' => $group['op'],
                            'updated_at'  => Carbon::now()
                        ]);
                }
            }
            DB::commit();
            return response()->json('success', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }

        //20385416
    }
}
