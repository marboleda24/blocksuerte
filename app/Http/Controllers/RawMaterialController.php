<?php

namespace App\Http\Controllers;

use App\Models\EntradaOC;
use App\Models\PurchaseOrderCode;
use App\Models\RawMaterial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RawMaterialController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:other-applications.raw-material');
    }

    /**
     * @return Response
     */
    public function index()
    {
        $data = RawMaterial::with(['receivedBy' => function ($query) {
            $query->select('id', 'name');
        }])
            ->select('oc', 'received_by', 'created_at')
            ->distinct()
            ->get();

        //return $data;
        return Inertia::render('Applications/RawMaterials/Index', [
            'data' => $data,
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $users = User::orderBy('name', 'asc')->get();

        return Inertia::render('Applications/RawMaterials/Create', [
            'users' => $users,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_order(Request $request): JsonResponse
    {
        if (strlen($request->q) > 0) {
            $data = PurchaseOrderCode::with('user', 'vendor')
                ->where('CreationDate', '>=', Carbon::now()->subYears(2)->format('Y-m-d\Th:m:s.v'))
                ->where('ORDNUM_16', 'like', $request->q.'%')
                ->orderBy('ORDNUM_16', 'desc')
                ->take(100)
                ->get();

            return response()->json($data, 200);
        } else {
            return response()->json([], 200);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function detail_order(Request $request): JsonResponse
    {
        try {
            $data = EntradaOC::with('registro')
                ->where('OC', 'like', $request->order.'%')
                ->get();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse*
     */
    public function store(Request $request): JsonResponse
    {
        try {
            foreach ($request->items as $row) {
                RawMaterial::updateOrCreate(
                    [
                        'oc' => substr($row['oc'], 0, -4),
                        'entry' => $row['item'],
                        'entry_id' => $row['id_entrada'],
                    ],
                    [

                        'dimension' => $row['dimension'] ? 1 : 0,
                        'appearance' => $row['apariencia'] ? 1 : 0,
                        'weight' => $row['peso'] ? 1 : 0,
                        'observation' => $row['observaciones'],
                        'received_by' => $request['received_by'],
                        'created_by' => auth()->user()->id,
                    ]);
            }

            return response()->json('success', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
