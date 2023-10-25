<?php

namespace App\Http\Controllers\ElectronicBilling\National;

use App\Http\Controllers\Controller;
use App\Models\Dian\DebitNote;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DebitNoteController extends Controller
{
    /**
     * @param $entity
     * @return Response
     */
    public function index($entity): Response
    {
        return Inertia::render('Applications/ElectronicBilling/National/DebitNote', [
            'entity' => $entity
        ]);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function search_by_date(Request $request, $entity): JsonResponse
    {
        $date = explode(' - ', $request->date);

        if($entity === 'CIEV'){
            $data = DebitNote::with('apiDocument')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->where('MONEDA', '=', 'COP')
                ->orderBy('numero', 'asc')
                ->get();

            return response()->json($data, 200);
        }else{
            $data = DebitNote::with('apiDocument')
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d'), Carbon::parse($date[1])->format('Y-m-d')])
                ->where('MONEDA', '=', 'COP')
                ->orderBy('numero', 'asc')
                ->get();

            return response()->json($data, 200);
        }



    }
}
