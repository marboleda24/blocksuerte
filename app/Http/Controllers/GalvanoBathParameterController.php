<?php

namespace App\Http\Controllers;

use App\Models\GalvanoBathParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GalvanoBathParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:galvano-bath-parameters.register')->only('register', 'search_op', 'store');
        $this->middleware('permission:galvano-bath-parameters.index')->only('index');
    }

    public function index()
    {
        $records = GalvanoBathParameter::with('user', 'product')->get();

        return Inertia::render('Applications/GalvanoBathParameter/Index', [
            'records' => $records,
        ]);
    }

    /**
     * @return Response
     */
    public function register()
    {
        return Inertia::render('Applications/GalvanoBathParameter/Register');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search_op(Request $request)
    {
        $q = $request->get('q');

        $result = DB::connection('MAX')->table('CIEV_V_OP')
            ->where('ORDNUM_10', 'LIKE', "%$q%")
            ->get();

        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $record = new GalvanoBathParameter($request->all());
            $record->user_id = Auth::id();
            $record->save();

            return response()->json('success', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
