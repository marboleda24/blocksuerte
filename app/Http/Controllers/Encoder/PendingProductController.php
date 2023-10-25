<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\DesignRequirementProduct;
use App\Models\DetailOrder;
use App\Models\EncoderCode;
use App\Models\EncoderDecorativeOption;
use App\Models\EncoderGalvanicFinish;
use App\Models\PartMaster;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Masterminds\HTML5\Exception;
use Throwable;

class PendingProductController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $data = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material',
            'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish',
            'decorative_option', 'brand')
            ->where('type', '=', 'final')
            ->where('state', '=', 'clone')
            ->get();

        $buyers = DB::connection('MAXP')
            ->table('Buyers')
            ->select('BUYID_95 as id', 'BUYNME_95 as name')
            ->get();

        $planners = DB::connection('MAXP')
            ->table('Planners')
            ->select('PLNID_63 as id', 'NAME_63 as name')
            ->get();

        $warehouses = DB::connection('MAXP')
            ->table('Stock_Master')
            ->select('STK_05 as id', 'DESC_05 as name')
            ->get();

        $class_codes = DB::connection('MAXP')
            ->table('Class_Codes')
            ->select('CLSCDE_47 as id', 'DESC_47 as name')
            ->get();

        $comfort_codes = DB::connection('MAXP')
            ->table('Commodity_Codes')
            ->select('COMCDE_48 as id', 'DESC_48 as name')
            ->get();

        $account_types = DB::connection('MAXP')
            ->table('Account_Types')
            ->select('ACTTYP_104 as id', 'DESCRPTN_104 as name')
            ->get();

        $galvanic_finishes = EncoderGalvanicFinish::orderBy('name')
            ->where('state', '=', 1)
            ->get();

        $decorative_options = EncoderDecorativeOption::orderBy('name')
            ->where('state', '=', 1)
            ->get();

        return Inertia::render('Applications/Encoder/PendingProduct', [
            'data' => $data,
            'buyers' => $buyers,
            'planners' => $planners,
            'warehouses' => $warehouses,
            'class_codes' => $class_codes,
            'comfort_codes' => $comfort_codes,
            'account_types' => $account_types,
            'galvanic_finishes' => $galvanic_finishes,
            'decorative_options' => $decorative_options,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function post(Request $request)
    {
        DB::beginTransaction();
        DB::connection('MAXP')->beginTransaction();
        try {
            $from_product = PartMaster::where('PRTNUM_01', '=', $request->from_piece)
                ->first();

            $from_product_structure = DB::connection('MAXP')
                ->table('Product_Structure')
                ->where('PARPRT_02', '=', $request->from_piece)
                ->get();

            $from_part_routing = DB::connection('MAXP')
                ->table('Part_Routing')
                ->where('PRTNUM_12', '=', $request->from_piece)
                ->get();

            $from_part_sales = DB::connection('MAXP')
                ->table('Part_Sales')
                ->where('PRTNUM_29', '=', $request->from_piece)
                ->get();

            $current_date = Carbon::now()->format('Y-m-d\Th:m:s.v');

            $design_requirement_product = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement.detail',
                'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish', 'decorative_option')
                ->where('state', '=', 'clone')
                ->where('code', '=', $request->master['piece'])
                ->firstOrFail();

            $galvanic_finish = EncoderGalvanicFinish::find($design_requirement_product->galvanic_finish_code);
            $decorative_option = EncoderDecorativeOption::find($design_requirement_product->decorative_option_code);

            $final_product = generate_code_description($design_requirement_product->product_type->code, $design_requirement_product->line,
                $design_requirement_product->subline, $design_requirement_product->feature->abbreviation, $design_requirement_product->material,
                $design_requirement_product->measurement->detail, $design_requirement_product->art_code, $galvanic_finish->abbreviation, $decorative_option->abbreviation);

            $description = strlen($final_product['description']) > 60
                ? str_split($final_product['description'], 60)
                : $final_product['description'];

            DB::connection('MAXP')
                ->table('Part_Master')
                ->insert([
                    'PRTNUM_01' => $final_product['code'],
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
                    'PRDDTE_01' => $current_date,
                    'WGTDEM_01' => $request->master['udm_cost'],
                    'WGT_01' => $request->inventory['average_weight'],
                    'EXCDTE_01' => $current_date,
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
                    'CSTTYP_01' => $from_product->CSTTYP_01,
                    'CSTDTE_01' => $current_date,
                    'CSTUOM_01' => 'KG', /*validar*/
                    'CSTCNV_01' => '1',
                    'MATL_01' => '0',
                    'LABOR_01' => $from_product->LABOR_01,
                    'VOH_01' => $from_product->VOH_01,
                    'FOH_01' => $from_product->FOH_01,
                    'QUMMAT_01' => $from_product->QUMMAT_01,
                    'QUMLAB_01' => $from_product->QUMLAB_01,
                    'QUMVOH_01' => $from_product->QUMVOH_01,
                    'QUMFOH_01' => '0',
                    'HRS_01' => $from_product->HRS_01,
                    'QUMHRS_01' => $from_product->QUMHRS_01,
                    'ALPHA_01' => '0',
                    'QUMSUB_01' => '0',
                    'PURUOM_01' => $from_product->PURUOM_01,
                    'PURCNV_01' => $from_product->PURCNV_01,
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
                    'PERDAY_01' => $from_product->PERDAY_01,
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
                    'XDFDTE_01' => $current_date,
                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                    'CreationDate' => $current_date,
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

            DB::connection('MAXP')
                ->table('Activity_index')
                ->insert([
                    'LLC_03' => $request->engineering['cbn'],
                    'PRTNUM_03' => $final_product['code'],
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
                    'CreationDate' => $current_date,
                ]);

            foreach ($from_product_structure as $ep) {
                DB::connection('MAXP')
                    ->table('Product_Structure')
                    ->insert([
                        'PARPRT_02' => $final_product['code'],
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
                        'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                        'CreationDate' => $current_date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'ALTCDE_02' => '',
                    ]);
            }

            foreach ($from_part_routing as $pr) {
                DB::connection('MAXP')
                    ->table('Part_Routing')
                    ->insert([
                        'PRTNUM_12' => $final_product['code'],
                        'OPRSEQ_12' => $pr->OPRSEQ_12,
                        'OPRID_12' => $pr->OPRID_12,
                        'WRKCTR_12' => $pr->WRKCTR_12,
                        'OPRDES_12' => $pr->OPRDES_12,
                        'RUNTIM_12' => $pr->RUNTIM_12,
                        'SETTIM_12' => $pr->SETTIM_12,
                        'REVDTE_12' => $current_date,
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
                        'EFFDTE_12' => $current_date,
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
                        'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                        'CreationDate' => $current_date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'ALTCDE_12' => '',
                    ]);
            }

            foreach ($from_part_sales as $ps) {
                DB::connection('MAXP')
                    ->table('Part_Sales')
                    ->insert([
                        'PRTNUM_29' => $final_product['code'],
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
                        'XDFDTE_29' => $current_date,
                        'XDFTXT_29' => '',
                        'FILLER_29' => '',
                        'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                        'CreationDate' => $current_date,
                        'ModifiedBy' => '',
                        'ModificationDate' => '',
                        'MANPRC_29' => $ps->MANPRC_29,
                        'WARRES_29' => $ps->WARRES_29,
                    ]);
            }

            EncoderCode::create([
                'code' => $final_product['code'],
                'description' => $final_product['description'],
                'product_type_code' => $design_requirement_product->product_type_code,
                'line_code' => $design_requirement_product->line_code,
                'subline_code' => $design_requirement_product->subline_code,
                'feature_code' => $design_requirement_product->feature_code,
                'material_id' => $design_requirement_product->material_id,
                'measurement_id' => $design_requirement_product->measurement_id,
                'galvanic_finish_code' => $design_requirement_product->galvanic_finish_code,
                'decorative_option_code' => $design_requirement_product->decorative_option_code,
                'comments' => 'Generado desde requerimientos',
                'art_code' => $design_requirement_product->art_code,
                'created_by' => Auth::id(),
                'state' => 1,
            ]);

            $change_products = DetailOrder::where('product', '=', $request->master['piece'])->get();

            foreach ($change_products as $change_product) {
                $change_product->product = $final_product['code'];
                $change_product->save();
            }

            $design_requirement_product->state = 'finish';
            $design_requirement_product->save();

            DB::commit();
            DB::connection('MAXP')->commit();

            $data = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature',
                'material.material', 'measurement.detail', 'measurement.detail.characteristic',
                'measurement.detail.unit', 'galvanic_finish', 'decorative_option', 'brand')
                ->where('type', '=', 'final')
                ->where('state', '=', 'clone')
                ->get();

            return response()->json([
                'data' => $data,
                'product' => (object)$final_product
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            DB::connection('MAXP')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
    
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request){
        DB::beginTransaction();
        try {
            $design_requirement_product = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement.detail',
                'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish', 'decorative_option')
                ->where('code', '=', $request->product['code'])
                ->firstOrFail();

            $design_requirement_product->galvanic_finish_code = $request->galvanic_finish_code;
            $design_requirement_product->decorative_option_code = $request->decorative_option_code;


            $galvanic_finish = EncoderGalvanicFinish::find($request->galvanic_finish_code);
            $decorative_option = EncoderDecorativeOption::find($request->decorative_option_code);

            $final_product = generate_code_description($design_requirement_product->product_type->code, $design_requirement_product->line,
                $design_requirement_product->subline, $design_requirement_product->feature->abbreviation, $design_requirement_product->material,
                $design_requirement_product->measurement->detail, $design_requirement_product->art_code, $galvanic_finish->abbreviation, $decorative_option->abbreviation);

            $design_requirement_product->description = $final_product['description'];
            $design_requirement_product->save();

            $data = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement.detail', 'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish', 'decorative_option')
                ->where('type', '=', 'final')
                ->where('state', '=', 'clone')
                ->get();

            DB::commit();
            return response()->json($data, 200);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}
