<?php

namespace App\Http\Controllers\Queries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductionOrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:queries.production-order-status');
    }

    /**
     * @return Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('super-admin') || auth()->user()->can('queries.production-order-status.show-all')) {
            $query = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP as EOP')
                ->join('CIEV_V_OP_OV as POV', 'EOP.ORDNUM_14', '=', 'POV.OP')
                ->select('ORDNUM_14', 'WRKCTR_14', 'OPRDES_14', 'PRTNUM_14', 'PMDES1_01', 'EOP.OV', 'QTYREM_14', 'QTYCOM_14', 'Cliente', 'MOVDTE_14', 'FechaOV', 'QUECDE_14', 'OC')
                ->where('Estado', '=', 3)
                ->where('STATUS_10', '=', 3)
                ->where('QUECDE_14', '=', 'Y')
                ->orderBy('ORDNUM_14')
                ->orderBy('Cliente')
                ->orderBy('WRKCTR_14')
                ->get();
        } else {
            $query = DB::connection('MAX')
                ->table('CIEV_V_EstadoOP as EOP')
                ->join('CIEV_V_OP_OV as POV', 'EOP.ORDNUM_14', '=', 'POV.OP')
                ->select('ORDNUM_14', 'WRKCTR_14', 'OPRDES_14', 'PRTNUM_14', 'PMDES1_01', 'EOP.OV', 'QTYREM_14', 'QTYCOM_14', 'Cliente', 'MOVDTE_14', 'FechaOV', 'QUECDE_14', 'OC')
                ->where('Vendedor', '=', auth()->user()->vendor_code)
                ->where('Estado', '=', 3)
                ->where('STATUS_10', '=', 3)
                ->where('QUECDE_14', '=', 'Y')
                ->orderBy('ORDNUM_14')
                ->orderBy('Cliente')
                ->orderBy('WRKCTR_14')
                ->get();
        }

        return Inertia::render('Applications/Queries/ProductionOrderStatus', [
            'query' => $query,
        ]);
    }
}
