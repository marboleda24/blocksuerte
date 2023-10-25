<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\EncoderCode;
use App\Models\EncoderProduct;
use App\Models\PartMaster;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use PHPUnit\Exception;
use Throwable;

class ClonerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.encoder.cloner');
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $buyers = DB::connection('MAX')
            ->table('Buyers')
            ->select('BUYID_95 as id', 'BUYNME_95 as name')
            ->get();

        $planners = DB::connection('MAX')
            ->table('Planners')
            ->select('PLNID_63 as id', 'NAME_63 as name')
            ->get();

        $warehouses = DB::connection('MAX')
            ->table('Stock_Master')
            ->select('STK_05 as id', 'DESC_05 as name')
            ->get();

        $class_codes = DB::connection('MAX')
            ->table('Class_Codes')
            ->select('CLSCDE_47 as id', 'DESC_47 as name')
            ->get();

        $comfort_codes = DB::connection('MAX')
            ->table('Commodity_Codes')
            ->select('COMCDE_48 as id', 'DESC_48 as name')
            ->get();

        $account_types = DB::connection('MAX')
            ->table('Account_Types')
            ->select('ACTTYP_104 as id', 'DESCRPTN_104 as name')
            ->get();

        return Inertia::render('Applications/Encoder/Cloner', [
            'buyers' => $buyers,
            'planners' => $planners,
            'warehouses' => $warehouses,
            'class_codes' => $class_codes,
            'comfort_codes' => $comfort_codes,
            'account_types' => $account_types,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_product(Request $request): JsonResponse
    {
        try {
            $data = EncoderCode::where('state', '=', 1)
                ->where(function ($query) use ($request) {
                    $query->where('code', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('description', 'LIKE', '%' . $request->q . '%');
                })->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search_product_max(Request $request): JsonResponse
    {
        try {
            $data = PartMaster::where('STAENG_01', '=', '2')
                ->where(function ($query) use ($request) {
                    $query->where('PRTNUM_01', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('PMDES1_01', 'LIKE', '%' . $request->q . '%');
                })->take(40)
                ->orderBy('CreationDate')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            $from_piece = PartMaster::where('PRTNUM_01', '=', $request->from_piece)
                ->first();

            $date = Carbon::now()->format('Y-m-d\Th:m:s.v');

            $description = strlen($request->master['description']) > 60
                ? str_split($request->master['description'], 60)
                : $request->master['description'];

            DB::connection('MAX')
                ->table('Part_Master')
                ->insert([
                    'PRTNUM_01' => $request->master['piece'],
                    'TYPE_01' => $request->master['type'],
                    'CLSCDE_01' => $request->master['cod_class'],
                    'PLANID_01' => $request->master['planner'],
                    'COMCDE_01' => $request->master['comfort_code'],
                    'LLC_01' => $request->engineering['cbn'],
                    'PMDES1_01' => is_array($description) ? $description[0] : $description,
                    'PMDES2_01' => is_array($description) ? $description[1] : '',
                    'BOMUOM_01' => $request->master['umd_ldm'],
                    'STAENG_01' => $request->engineering['state'],
                    'STAACT_01' => '2',
                    'FRMPLN_01' => $request->planner['firm_plan'] ? 'Y' : 'N',
                    'PRDDTE_01' => $date,
                    'WGTDEM_01' => $request->master['udm_cost'],
                    'WGT_01' => $request->inventory['average_weight'],
                    'EXCDTE_01' => $date,
                    'EXCFLG_01' => 'N',
                    'DELSTK_01' => $request->master['warehouse'],
                    'CYCCDE_01' => $request->inventory['cycle_count']['code'],
                    'CYCNUM_01' => '0',
                    'CYCPER_01' => $request->inventory['cycle_count']['tolerance_percentage'],
                    'OBSOLT_01' => '0',
                    'CYCOOT_01' => '0',
                    'ORDPOL_01' => $request->planner['order_policy'],
                    'YIELD_01' => $request->engineering['yield'],
                    'ROP_01' => $request->planner['prd'],
                    'ROQ_01' => $request->planner['crd'],
                    'SAFSTK_01' => $request->planner['safety_inventory'],
                    'MINQTY_01' => $request->planner['order_quantity']['min'],
                    'MAXQTY_01' => $request->planner['order_quantity']['max'],
                    'MULQTY_01' => $request->planner['order_quantity']['multiple'],
                    'AVEQTY_01' => $request->planner['order_quantity']['average'],
                    'ISSMTD_01' => '0',
                    'ISSYTD_01' => '0',
                    'SALMTD_01' => '0',
                    'SALYTD_01' => '0',
                    'MFGLT_01' => $request->master['tc_manufacture'],
                    'MFGPIC_01' => $request->planner['manufacturing']['plan'],
                    'MFGOPR_01' => $request->planner['manufacturing']['manufacture'],
                    'MFGSTK_01' => $request->planner['manufacturing']['stock'],
                    'PURLT_01' => $request->planner['purchases']['cycle_time'],
                    'PURPIC_01' => $request->planner['purchases']['plan'],
                    'PUROPR_01' => $request->planner['purchases']['purchase'],
                    'PURSTK_01' => $request->planner['purchases']['stock'],
                    'PRICE_01' => '0',
                    'COST_01' => '0',
                    'CSTTYP_01' => $from_piece->CSTTYP_01,
                    'CSTDTE_01' => $date,
                    'CSTUOM_01' => 'KG', /*validar*/
                    'CSTCNV_01' => '1',
                    'MATL_01' => '0',
                    'LABOR_01' => $from_piece->LABOR_01,
                    'VOH_01' => $from_piece->VOH_01,
                    'FOH_01' => $from_piece->FOH_01,
                    'QUMMAT_01' => $from_piece->QUMMAT_01,
                    'QUMLAB_01' => $from_piece->QUMLAB_01,
                    'QUMVOH_01' => $from_piece->QUMVOH_01,
                    'QUMFOH_01' => '0',
                    'HRS_01' => $from_piece->HRS_01,
                    'QUMHRS_01' => $from_piece->QUMHRS_01,
                    'ALPHA_01' => '0',
                    'QUMSUB_01' => '0',
                    'PURUOM_01' => $from_piece->PURUOM_01,
                    'PURCNV_01' => $from_piece->PURCNV_01,
                    'SCRAP_01' => $request->engineering['waste'],
                    'BUYER_01' => $request->master['buyer'],
                    'INSRQD_01' => $request->inventory['requires_inspection'] ? 'Y' : 'N',
                    'ONHAND_01' => '0',
                    'NONNET_01' => '0',
                    'SCHCDE_01' => 'B',
                    'REVLEV_01' => $request->master['revision_level'] ?? '',
                    'ACTTYP_01' => $request->engineering['account_type'],
                    'ACTCDE_01' => '1',
                    'SCHFLG_01' => $request->planner['program'],
                    'MPNFLG_01' => 'N',
                    'MATLXY_01' => '0',
                    'CRPHLT_01' => $request->planner['critical_tc'],
                    'LOTTRK_01' => $request->inventory['batch_control'] ? 'Y' : 'N',
                    'MULREC_01' => $request->inventory['multi_inputs'] ? 'Y' : 'N',
                    'SERTRK_01' => $request->inventory['control_ns'] ? 'Y' : 'N',
                    'LOTSFC_01' => $request->inventory['cdp_lot'] ? 'Y' : 'N',
                    'SHLIFE_01' => $request->inventory['expiration_days'],
                    'DRANUM_01' => $request->engineering['plane'] ?? '',
                    'DELLOC_01' => $request->master['zone'] ?? '',
                    'PERDAY_01' => $from_piece->PERDAY_01,
                    'ALLOC_01' => '0',
                    'JOBEXP_01' => 'Y',
                    'RNDRQS_01' => 'N',
                    'EXCREC_01' => $request->inventory['excess_tickets'],
                    'INDDEM_01' => 'N',
                    'SUPCDE_01' => $request->planner['critical_piece'] ? 'Y' : 'N',
                    'CYCDOL_01' => $request->inventory['cycle_count']['tolerance'],
                    'STDVOH_01' => '0',
                    'XDFINT_01' => '0',
                    'XDFFLT_01' => '0',
                    'XDFDTE_01' => $date,
                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                    'CreationDate' => $date,
                    'SUBCST_01' => '',
                    'CURREV_01' => '',
                    'RECVEN_01' => '',
                    'RTEREV_01' => '',
                    'VIEWER_01' => '',
                    'MCOMP_01' => '',
                    'MSITE_01' => '',
                    'UDFKEY_01' => '',
                    'UDFREF_01' => '',
                    'LSTECN_01' => '',
                    'ROHS_01' => $request->planner['rump'] ? 'Y' : 'N',
                    'NCNR_01' => $request->planner['ncnd'] ? 'Y' : 'N',
                ]);

            DB::connection('MAX')
                ->table('Activity_index')
                ->insert([
                    'LLC_03' => $request->engineering['cbn'],
                    'PRTNUM_03' => $request->master['piece'],
                    'BOMFLG_03' => 'N',
                    'MPSFLG_03' => 'N',
                    'MRPFLG_03' => 'N',
                    'CSTFLG_03' => 'N',
                    'PL1FLG_03' => 'N',
                    'MCOMP_03' => '',
                    'MSITE_03' => '',
                    'UDFKEY_03' => '',
                    'UDFREF_03' => '',
                    'FILLER_03' => '',
                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                    'CreationDate' => $date,
                ]);

            $from_product_structure = DB::connection('MAX')
                ->table('Product_Structure')
                ->where('PARPRT_02', '=', $request->from_piece)
                ->get();

            foreach ($from_product_structure as $ep) {
                DB::connection('MAX')
                    ->table('Product_Structure')
                    ->insert([
                        'PARPRT_02' => $request->master['piece'],
                        'COMPRT_02' => $ep->COMPRT_02,
                        'EFFDTE_02' => Carbon::parse($ep->EFFDTE_02)->format('Y-m-d\T00:00:00.000'),
                        'FILL01_02' => $ep->FILL01_02,
                        'QTYPER_02' => $ep->QTYPER_02,
                        'QTYCDE_02' => $ep->QTYCDE_02,
                        'LTOSET_02' => $ep->LTOSET_02,
                        'TYPCDE_02' => $ep->TYPCDE_02,
                        'SCRAP_02' => $ep->SCRAP_02,
                        'ECN_02' => $ep->ECN_02,
                        'ACTDTE_02' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                        'FILL02_02' => $ep->FILL02_02,
                        'ALTPRT_02' => $ep->ALTPRT_02,
                        'REFDES_02' => $ep->REFDES_02,
                        'MPNSTR_02' => $ep->MPNSTR_02,
                        'MCOMP_02' => $ep->MCOMP_02,
                        'MSITE_02' => $ep->MSITE_02,
                        'UDFKEY_02' => $ep->UDFKEY_02,
                        'UDFREF_02' => $ep->UDFREF_02,
                        'XDFINT_02' => $ep->XDFINT_02,
                        'XDFFLT_02' => $ep->XDFFLT_02,
                        'XDFBOL_02' => $ep->XDFBOL_02,
                        'XDFDTE_02' => '',
                        'XDFTXT_02' => $ep->XDFTXT_02,
                        'FILLER_02' => $ep->FILLER_02,
                        'CreatedBy' => 'evpiu-' . auth()->user()->username,
                        'CreationDate' => $date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'ALTCDE_02' => '',
                    ]);
            }

            $from_part_routing = DB::connection('MAX')
                ->table('Part_Routing')
                ->where('PRTNUM_12', '=', $request->from_piece)
                ->get();

            foreach ($from_part_routing as $pr) {
                DB::connection('MAX')
                    ->table('Part_Routing')
                    ->insert([
                        'PRTNUM_12' => $request->master['piece'],
                        'OPRSEQ_12' => $pr->OPRSEQ_12,
                        'OPRID_12' => $pr->OPRID_12,
                        'WRKCTR_12' => $pr->WRKCTR_12,
                        'OPRDES_12' => $pr->OPRDES_12,
                        'RUNTIM_12' => $pr->RUNTIM_12,
                        'SETTIM_12' => $pr->SETTIM_12,
                        'REVDTE_12' => $date,
                        'FILL01_12' => $pr->FILL01_12,
                        'OPRTYP_12' => $pr->OPRTYP_12,
                        'STDTYP_12' => $pr->STDTYP_12,
                        'QTYPER_12' => $pr->QTYPER_12,
                        'TOOL_12' => $pr->TOOL_12,
                        'SUBCST_12' => $pr->SUBCST_12,
                        'PSCRAP_12' => $pr->PSCRAP_12,
                        'ASCRAP_12' => $pr->ASCRAP_12,
                        'SETEXT_12' => $pr->SETEXT_12,
                        'SETINC_12' => $pr->SETINC_12,
                        'MOVDAY_12' => $pr->MOVDAY_12,
                        'APRDBY_12' => $pr->APRDBY_12,
                        'EFFDTE_12' => $date,
                        'MCOMP_12' => $pr->MCOMP_12,
                        'MSITE_12' => $pr->MSITE_12,
                        'UDFKEY_12' => $pr->UDFKEY_12,
                        'UDFREF_12' => $pr->UDFREF_12,
                        'SERVICEID_12' => $pr->SERVICEID_12,
                        'PRIVENID_12' => $pr->PRIVENID_12,
                        'RTGGRP_12' => $pr->RTGGRP_12,
                        'XDFINT_12' => $pr->XDFINT_12,
                        'XDFFLT_12' => $pr->XDFFLT_12,
                        'XDFBOL_12' => $pr->XDFBOL_12,
                        'XDFDTE_12' => null,
                        'XDFTXT_12' => $pr->XDFTXT_12,
                        'FILLER_12' => $pr->FILLER_12,
                        'CreatedBy' => 'evpiu-' . auth()->user()->username,
                        'CreationDate' => $date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'ALTCDE_12' => '',
                    ]);
            }

            $from_part_sales = DB::connection('MAX')
                ->table('Part_Sales')
                ->where('PRTNUM_29', '=', $request->from_piece)
                ->get();

            foreach ($from_part_sales as $ps) {
                DB::connection('MAX')
                    ->table('Part_Sales')
                    ->insert([
                        'PRTNUM_29' => $request->master['piece'],
                        'SLSCAT_29' => '',
                        'PMDES1_29' => is_array($description) ? $description[0] : $description,
                        'PMDES2_29' => is_array($description) ? $description[1] : '',
                        'STK_29' => $request->master['warehouse'],
                        'TAXABL_29' => $ps->TAXABL_29,
                        'BOMUOM_29' => $request->master['umd_ldm'],
                        'SLSUOM_29' => $ps->SLSUOM_29,    /*Hay que crear un input para este campo y poderlo editar desde el frontend*/
                        'SLSCNV_29' => $ps->SLSCNV_29,   /*Hay que crear un input para este campo y poderlo editar desde el frontend*/
                        'PRICE_29' => '0',
                        'BREAK1_29' => '0',
                        'DISC1_29' => '0',
                        'PRICE1_29' => '0',
                        'BREAK2_29' => '0',
                        'DISC2_29' => '0',
                        'PRICE2_29' => '0',
                        'BREAK3_29' => '0',
                        'DISC3_29' => '0',
                        'PRICE3_29' => '0',
                        'BREAK4_29' => '0',
                        'DISC4_29' => '0',
                        'PRICE4_29' => '0',
                        'BREAK5_29' => '0',
                        'DISC5_29' => '0',
                        'PRICE5_29' => '0',
                        'BREAK6_29' => '0',
                        'DISC6_29' => '0',
                        'PRICE6_29' => '0',
                        'BREAK7_29' => '0',
                        'DISC7_29' => '0',
                        'PRICE7_29' => '0',
                        'BREAK8_29' => '0',
                        'DISC8_29' => '0',
                        'PRICE8_29' => '0',
                        'BREAK9_29' => '0',
                        'DISC9_29' => '0',
                        'PRICE9_29' => '0',
                        'QTYMTD_29' => '0',
                        'SLSMTD_29' => '0',
                        'CSTMTD_29' => '0',
                        'QTYYTD_29' => '0',
                        'SLSYTD_29' => '0',
                        'CSTYTD_29' => '0',
                        'QTYLYR_29' => '0',
                        'SLSLYR_29' => '0',
                        'CSTLYR_29' => '0',
                        'QTYCOM_29' => '0',
                        'CRTLTO_29' => intval($request->planner['critical_tc']),
                        'AUTOMS_29' => $ps->AUTOMS_29,
                        'APLDSC_29' => $ps->APLDSC_29,
                        'PRDLIN_29' => $ps->PRDLIN_29,
                        'HISFLG_29' => $ps->HISFLG_29,
                        'WARFLG_29' => $ps->WARFLG_29,
                        'LABWAR_29' => '0',
                        'MATWAR_29' => '0',
                        'RETMTD_29' => '0',
                        'RETYTD_29' => '0',
                        'UNWRPL_29' => '0',
                        'UNWREP_29' => '0',
                        'OUWRPL_29' => '0',
                        'OUWREP_29' => '0',
                        'COMMIS_29' => '0',
                        'TAXCDE_29' => $ps->TAXCDE_29,
                        'TAXCDE2_29' => '',
                        'TAXCDE3_29' => '',
                        'MCOMP_29' => '',
                        'MSITE_29' => '',
                        'UDFKEY_29' => '',
                        'UDFREF_29' => '',
                        'ALWBCK_29' => $ps->ALWBCK_29,
                        'AUTOMF_29' => $ps->AUTOMF_29,
                        'XDFINT_29' => '0',
                        'XDFFLT_29' => '0',
                        'XDFBOL_29' => '0',
                        'XDFDTE_29' => $date,
                        'XDFTXT_29' => '',
                        'FILLER_29' => '',
                        'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                        'CreationDate' => $date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'MANPRC_29' => $ps->MANPRC_29,
                        'WARRES_29' => $ps->WARRES_29,
                    ]);
            }

            EncoderProduct::create([
                'old_product' => $request->from_piece,
                'new_product' => $request->master['piece'],
                'user_id' => Auth::id(),
            ]);


            $query_check = DB::connection('MAX')
                ->table('Part_Routing')
                ->where('SUBCST_12', '=', '0')
                ->whereIn('WRKCTR_12', ['AFUER', 'COMVA', 'EEXT', 'ENRRE', 'LASEE'])
                ->where('PRTNUM_12', '=', $request->from_piece)
                ->first();

            $message = "Producto Clona con exito";

            if ($query_check) {
                Mail::to(['costos@estradavelasquez.com'])
                    ->bcc('dcorrea@estradavelasquez.com')
                    ->send(new SystemNotificationMail(
                            "Producto sin costo en subcontrato",
                            "Producto sin costo en subcontrato",
                            "EVPIU Le informa que el producto {$query_check->PRTNUM_12} tiene costo 0 en el subcontrato del centro de trabajo {$query_check->WRKCTR_12}, por favor corrija este valor en los productos {$query_check->PRTNUM_12} y {$request->master['piece']}.")
                    );
                $message = "Los productos {$query_check->PRTNUM_12} y {$request->master['piece']} tienen costo 0 en el subcontrato del centro de trabajo {$query_check->WRKCTR_12}, por favor absténgase de generar ordenes de produccion hasta corregir esta inconsistencia";
            }

            DB::connection('MAX')->commit();
            return response()->json($message, 200);
        } catch (\Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
