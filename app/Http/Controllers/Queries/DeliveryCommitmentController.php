<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryCommitmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:queries.delivery-commitment');
    }

    /**
     * @return Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('queries.delivery-commitment.show-all')) {
            $query = DB::connection('MAX')
                ->table('CIEV_V_FechasEntregaCliente')
                ->orderBy('DIAS_FALTANTES')
                ->orderBy('OV')
                ->orderBy('LINEA')
                ->orderBy('ITEM')
                ->get();
        } else {
            $query = DB::connection('MAX')
                ->table('CIEV_V_FechasEntregaCliente')
                ->where('VENDEDOR', '=', auth()->user()->vendor_code)
                ->orderBy('DIAS_FALTANTES')
                ->orderBy('OV')
                ->orderBy('LINEA')
                ->orderBy('ITEM')
                ->get();
        }

        return Inertia::render('Applications/Queries/DeliveryCommitment', [
            'query' => $query,
        ]);
    }
}
