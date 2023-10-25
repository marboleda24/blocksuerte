<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use App\Models\DesignRequirementArt;
use App\Models\DesignRequirementProduct;
use App\Models\EncoderCode;
use App\Models\EncoderDecorativeOption;
use App\Models\EncoderGalvanicFinish;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class NewProductController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function fields()
    {
        try {
            $galvanic_finishes = EncoderGalvanicFinish::orderBy('name')
                ->where('state', '=', 1)
                ->get();

            $decorative_options = EncoderDecorativeOption::orderBy('name')
                ->where('state', '=', 1)
                ->get();

            return response()->json([
                'galvanic_finishes' => $galvanic_finishes,
                'decorative_options' => $decorative_options
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function product(Request $request)
    {
        try {
            $product = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement.detail',
                'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish', 'decorative_option')
                ->where('state', '=', 'NA')
                ->where('description', '=', $request->product_description)
                ->first();

            $arts = DesignRequirementProduct::where('father_id', '=', $product->id)
                ->where('type', '=', 'child')
                ->pluck('art_code');

            return response()->json([
                'product' => $product,
                'arts' => $arts
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $art
     * @return JsonResponse
     */
    public function search_art($art)
    {
        try {
            $query = DesignRequirementArt::where('code', 'LIKE', "%$art%")
                ->get()
                ->toArray();

            return response()->json([
                "items" => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param bool $generate
     * @return JsonResponse
     */
    public function store_product_v2(Request $request, bool $generate = false)
    {
        DB::beginTransaction();
        try {
            $existProduct = DesignRequirementProduct::with('product_type', 'line', 'subline', 'feature', 'material.material', 'measurement.detail',
                'measurement.detail.characteristic', 'measurement.detail.unit', 'galvanic_finish', 'decorative_option')
                ->where('description', '=', $request->product_description)
                ->first();

            $galvanic_finish = EncoderGalvanicFinish::find($request->galvanic_finish_code);
            $decorative_option = EncoderGalvanicFinish::find($request->decorative_option_code);
            $measurement = DesignRequirementProduct::with('measurement.detail')
                ->where('art_code', '=', $request->art_code)
                ->where('type', '=', 'child')
                ->first();

            $new_description = generate_code_description($existProduct->product_type_code, $existProduct->line, $existProduct->subline,
                $existProduct->feature->abbreviation, $existProduct->material, $measurement->measurement->detail, $request->art_code,
                $galvanic_finish->abbreviation, $decorative_option->abbreviation);

            $newProduct = $existProduct->replicate();
            $newProduct->code = strtoupper(Str::random(10));
            $newProduct->description = $new_description['description'];
            $newProduct->galvanic_finish_code = $request->galvanic_finish_code;
            $newProduct->decorative_option_code = $request->decorative_option_code;
            $newProduct->measurement_id = $measurement->measurement->id;
            $newProduct->art_code = $request->art_code;
            $newProduct->type = 'final';
            $newProduct->state = 'clone';
            $newProduct->save();

            DB::commit();

            Mail::to('dcorrea@estradavelasquez.com')
                ->send(new SystemNotificationMail('Producto pendiente',
                    'Producto pendiente',
                    "EVPIU le informa que tiene un producto pendiente por clonación de estructuras. Le recordamos que esta acción es importante para darle continuidad al proceso de pedidos"));

            $new_product = (object)[
                'field' => join(' – ', array_filter([$newProduct->code, $newProduct->description, $newProduct->art_code])),
                'code' => $newProduct->code,
                'origin' => 'MAX',
                'description' => $newProduct->description,
                'stock' => 0,
                'art_code' => $newProduct->art_code,
            ];

            return response()->json($new_product, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_measurement_by_art(Request $request)
    {
        try {
            $measurement = DesignRequirementProduct::with('measurement.detail')
                ->where('art_code', '=', $request->art)
                ->where('type', '=', 'child')
                ->first();

            if (!$measurement){
                throw new Exception('no se encontraron coincidencias', 500);
            }

            return response()->json([
                'measurement' => $measurement->measurement,
                'measurement_id' => $measurement->measurement->id
            ], 200);
        }catch (Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store_product(Request $request)
    {
        DB::beginTransaction();
        DB::connection('MAXP')->beginTransaction();
        try {
            $original_code = EncoderCode::where('code', '=', $request->code)
                ->first();

            $new_product_exist = EncoderCode::where('product_type_code', '=', $original_code->product_type_code)
                ->where('line_code', '=', $original_code->line_code)
                ->where('subline_code', '=', $original_code->subline_code)
                ->where('feature_code', '=', $original_code->feature_code)
                ->where('material_id', '=', $original_code->material_id)
                ->where('measurement_id', '=', $original_code->measurement_id)
                ->where('galvanic_finish_code', '=', $request->galvanic_finish_code)
                ->where('decorative_option_code', '=', $request->decorative_option_code)
                ->where('art_code', '=', $request->art_code)
                ->count();

            if ($new_product_exist === 0) {
                $galvanic_finish = EncoderGalvanicFinish::find($request->galvanic_finish_code);
                $decorative_option = EncoderDecorativeOption::find($request->decorative_option_code);

                $new_product = generate_code_description($original_code->product_type->code, $original_code->line, $original_code->subline, $original_code->feature->abbreviation, $original_code->material, $original_code->measurement->detail, $request->art_code, $galvanic_finish->abbreviation, $decorative_option->abbreviation);

                EncoderCode::create([
                    'code' => strtoupper($new_product['code']),
                    'description' => $new_product['description'],
                    'product_type_code' => $original_code->product_type->code,
                    'line_code' => $original_code->line->code,
                    'subline_code' => $original_code->subline->code,
                    'feature_code' => $original_code->feature->code,
                    'material_id' => $original_code->material->id,
                    'measurement_id' => $original_code->measurement->id,
                    'galvanic_finish_code' => $request->galvanic_finish_code,
                    'decorative_option_code' => $request->decorative_option_code,
                    'comments' => 'Creado desde requerimientos',
                    'art_code' => $request->art_code,
                    'created_by' => Auth::id(),
                    'state' => 1,
                ]);
            }

            $code = substr($request->code, 0, 6);

            $part_equal = DB::connection('MAXP')
                ->table('Part_Sales')
                ->where('PRTNUM_29', 'LIKE', "{$code}%")
                ->where('PMDES1_29', 'LIKE', "%" . $original_code->measurement->measurement . "%")
                ->first();

            if ($part_equal) {
                $part_master = DB::connection('MAXP')
                    ->table('Part_Master')
                    ->where('PRTNUM_01', '=', $part_equal->PRTNUM_29)
                    ->first();

                $product_structure = DB::connection('MAXP')
                    ->table('Product_Structure')
                    ->where('PARPRT_02', '=', $part_equal->PRTNUM_29)
                    ->get();

                $part_routing = DB::connection('MAXP')
                    ->table('Part_Routing')
                    ->where('PRTNUM_12', '=', $part_equal->PRTNUM_29)
                    ->get();

                $part_sales = DB::connection('MAXP')
                    ->table('Part_Sales')
                    ->where('PRTNUM_29', '=', $part_equal->PRTNUM_29)
                    ->get();

                $date = Carbon::now()->format('Y-m-d\Th:m:s.v');

                DB::connection('MAXP')
                    ->table('Part_Master')
                    ->insert([
                        'PRTNUM_01' => $new_product['code'],
                        'TYPE_01' => $part_master->TYPE_01,
                        'CLSCDE_01' => $part_master->CLSCDE_01,
                        'PLANID_01' => $part_master->PLANID_01,
                        'COMCDE_01' => $part_master->COMCDE_01,
                        'LLC_01' => $part_master->LLC_01,
                        'PMDES1_01' => $new_product['description'],
                        'PMDES2_01' => '',
                        'BOMUOM_01' => $part_master->BOMUOM_01,
                        'STAENG_01' => $part_master->STAENG_01,
                        'STAACT_01' => '2',
                        'FRMPLN_01' => $part_master->FRMPLN_01,
                        'PRDDTE_01' => $date,
                        'WGTDEM_01' => $part_master->WGTDEM_01,
                        'WGT_01' => $part_master->WGT_01,
                        'EXCDTE_01' => $date,
                        'EXCFLG_01' => 'N',
                        'DELSTK_01' => $part_master->DELSTK_01,
                        'CYCCDE_01' => $part_master->CYCCDE_01,
                        'CYCNUM_01' => '0',
                        'CYCPER_01' => $part_master->CYCPER_01,
                        'OBSOLT_01' => '0',
                        'CYCOOT_01' => '0',
                        'ORDPOL_01' => $part_master->ORDPOL_01,
                        'YIELD_01' => $part_master->YIELD_01,
                        'ROP_01' => $part_master->ROP_01,
                        'ROQ_01' => $part_master->ROQ_01,
                        'SAFSTK_01' => $part_master->SAFSTK_01,
                        'MINQTY_01' => $part_master->MINQTY_01,
                        'MAXQTY_01' => $part_master->MAXQTY_01,
                        'MULQTY_01' => $part_master->MULQTY_01,
                        'AVEQTY_01' => $part_master->AVEQTY_01,
                        'ISSMTD_01' => '0',
                        'ISSYTD_01' => '0',
                        'SALMTD_01' => '0',
                        'SALYTD_01' => '0',
                        'MFGLT_01' => $part_master->MFGLT_01,
                        'MFGPIC_01' => $part_master->MFGPIC_01,
                        'MFGOPR_01' => $part_master->MFGOPR_01,
                        'MFGSTK_01' => $part_master->MFGSTK_01,
                        'PURLT_01' => $part_master->PURLT_01,
                        'PURPIC_01' => $part_master->PURPIC_01,
                        'PUROPR_01' => $part_master->PUROPR_01,
                        'PURSTK_01' => $part_master->PURSTK_01,
                        'PRICE_01' => '0',
                        'COST_01' => '0',
                        'CSTTYP_01' => $part_master->CSTTYP_01,
                        'CSTDTE_01' => $date,
                        'CSTUOM_01' => 'KG', /*validar*/
                        'CSTCNV_01' => '1',
                        'MATL_01' => '0',
                        'LABOR_01' => $part_master->LABOR_01,
                        'VOH_01' => $part_master->VOH_01,
                        'FOH_01' => $part_master->FOH_01,
                        'QUMMAT_01' => $part_master->QUMMAT_01,
                        'QUMLAB_01' => $part_master->QUMLAB_01,
                        'QUMVOH_01' => $part_master->QUMVOH_01,
                        'QUMFOH_01' => '0',
                        'HRS_01' => $part_master->HRS_01,
                        'QUMHRS_01' => $part_master->QUMHRS_01,
                        'ALPHA_01' => '0',
                        'QUMSUB_01' => '0',
                        'PURUOM_01' => $part_master->PURUOM_01,
                        'PURCNV_01' => $part_master->PURCNV_01,
                        'SCRAP_01' => $part_master->SCRAP_01,
                        'BUYER_01' => $part_master->BUYER_01,
                        'INSRQD_01' => $part_master->INSRQD_01,
                        'ONHAND_01' => '0',
                        'NONNET_01' => '0',
                        'SCHCDE_01' => 'B',
                        'REVLEV_01' => $part_master->REVLEV_01,
                        'ACTTYP_01' => $part_master->ACTTYP_01,
                        'ACTCDE_01' => '1',
                        'SCHFLG_01' => $part_master->SCHFLG_01,
                        'MPNFLG_01' => 'N',
                        'MATLXY_01' => '0',
                        'CRPHLT_01' => $part_master->CRPHLT_01,
                        'LOTTRK_01' => $part_master->LOTTRK_01,
                        'MULREC_01' => $part_master->MULREC_01,
                        'SERTRK_01' => $part_master->SERTRK_01,
                        'LOTSFC_01' => $part_master->LOTSFC_01,
                        'SHLIFE_01' => $part_master->SHLIFE_01,
                        'DRANUM_01' => $part_master->DRANUM_01,
                        'DELLOC_01' => $part_master->DELLOC_01,
                        'PERDAY_01' => $part_master->PERDAY_01,
                        'ALLOC_01' => '0',
                        'JOBEXP_01' => 'Y',
                        'RNDRQS_01' => 'N',
                        'EXCREC_01' => $part_master->EXCREC_01,
                        'INDDEM_01' => 'N',
                        'SUPCDE_01' => $part_master->SUPCDE_01,
                        'CYCDOL_01' => $part_master->CYCDOL_01,
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
                        'ROHS_01' => $part_master->ROHS_01,
                        'NCNR_01' => $part_master->NCNR_01,
                    ]);

                DB::connection('MAXP')
                    ->table('Activity_index')
                    ->insert([
                        'LLC_03' => $part_master->LLC_01,
                        'PRTNUM_03' => $new_product['code'],
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

                foreach ($product_structure as $ep) {
                    DB::connection('MAXP')
                        ->table('Product_Structure')
                        ->insert([
                            'PARPRT_02' => $new_product['code'],
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
                            'CreationDate' => $date,
                            'ModifiedBy' => '',
                            'ModificationDate' => '',
                            'ALTCDE_02' => '',
                        ]);
                }

                foreach ($part_routing as $pr) {
                    DB::connection('MAXP')
                        ->table('Part_Routing')
                        ->insert([
                            'PRTNUM_12' => $new_product['code'],
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
                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                            'CreationDate' => $date,
                            'ModifiedBy' => '',
                            'ModificationDate' => '',
                            'ALTCDE_12' => '',
                        ]);
                }

                foreach ($part_sales as $ps) {
                    DB::connection('MAXP')
                        ->table('Part_Sales')
                        ->insert([
                            'PRTNUM_29' => $new_product['code'],
                            'SLSCAT_29' => '',
                            'PMDES1_29' => $new_product['description'],
                            'PMDES2_29' => '',
                            'STK_29' => $part_master->DELSTK_01,
                            'TAXABL_29' => $ps->TAXABL_29,
                            'BOMUOM_29' => $part_master->BOMUOM_01,
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
                            'CRTLTO_29' => intval($part_master->CRPHLT_01),
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

                DB::commit();
                DB::connection('MAXP')->rollBack();

                return response()->json([
                    "code" => 201,
                    "msg" => "El producto {$new_product['code']} – {$new_product['description']} ha sido creado y subido a MAX correctamente"
                ], 200);
            } else {
                Mail::to(['auxsistemas@estradavelasquez.com', 'sistemas@estradavelasquez.com'])
                    ->bcc('dcorrea@estradavelasquez.com')
                    ->send(new SystemNotificationMail('info', 'Producto pendiente por clonar', "El producto {$request->code} no encontro cantidato para la clonacion y debe de ser clonado manualmente"));

                DB::commit();
                DB::connection('MAXP')->rollBack();

                return response()->json([
                    "code" => 202,
                    "msg" => "El producto {$request->code} no tiene candidato para ser clonado en MAX, sera enviada una notificación al area de produccion para la clonación manual"
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            DB::connection('MAXP')->rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}
