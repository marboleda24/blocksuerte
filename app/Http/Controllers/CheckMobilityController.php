<?php

namespace App\Http\Controllers;

use App\Models\CheckMobility;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckMobilityController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $check_mobilities = CheckMobility::all();

        return Inertia::render('Applications/Mobility/Index', [
            'check_mobilities' => $check_mobilities
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            CheckMobility::create($request->all());

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function create()
    {
        $cities = DB::connection('DMS')
            ->table('y_ciudades')
            ->where('pais', '=', 169)
            ->select('descripcion')
            ->orderBy('descripcion')
            ->get();

        return Inertia::render('Applications/Mobility/Create', [
            'cities' => $cities
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $data = CheckMobility::find($id);
        return response()->json($data, 200);
    }
}


