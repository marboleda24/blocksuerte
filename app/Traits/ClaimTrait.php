<?php

namespace App\Traits;

use App\Models\ClaimHeader;
use App\Models\CustomerMaster;
use App\Models\RemissionHeader;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Throwable;

trait ClaimTrait
{
    /**
     * @param $id
     * @return Mpdf
     *
     * @throws MpdfException
     */
    protected function createPDF($id): Mpdf
    {
        $claim = ClaimHeader::with('user', 'invoice', 'items.product', 'items.new_product', 'causes',
            'logs.user', 'files', 'workplace', 'new_customer')
            ->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.claim.header'));
        $pdf->SetHTMLFooter(View::make('pdfs.claim.footer'));
        $pdf->WriteHTML(View::make('pdfs.claim.template', compact('claim')));
        $pdf->SetTitle("Reclamación #{$claim->consecutive} | EVPIU");
        $pdf->SetAuthor('EVPIU 3.0 - Todos los derechos reservados');

        return $pdf;
    }

    /**
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => 'Letter-P',
            'fontdata' => $fontData + [
                'Roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                    'B' => 'Roboto-Bold.ttf',
                    'I' => 'Roboto-Italic.ttf',
                ],
            ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 35,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/claim/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @param $claim
     * @return array
     * @throws Throwable
     */
    protected function credit_memo($claim): array
    {
        DB::connection('MAX')->beginTransaction();
        try {
            $max_order_num = DB::connection('MAX')
                    ->table('SO_Master')
                    ->whereIn('STYPE_27', ['CU', 'CR'])
                    ->max('ORDNUM_27') + 1;

            $ov = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas')
                ->where('NUMERO', '=', $claim->document)
                ->pluck('OV');

            $so_master = DB::connection('MAX')
                ->table('SO_Master')
                ->where('ORDNUM_27', '=', $ov)
                ->first();

            $so_detail = DB::connection('MAX')
                ->table('SO_Detail')
                ->where('ORDNUM_28', '=', $ov)
                ->orderBy('LINNUM_28')
                ->get();

            DB::connection('MAX')
                ->table('SO_Master')
                ->insert([
                    'ORDNUM_27' => $max_order_num,
                    'CUSTID_27' => $so_master->CUSTID_27,
                    'GLXREF_27' => '41209505',
                    'STYPE_27' => 'CR',
                    'STATUS_27' => '3',
                    'CUSTPO_27' => $claim->document,
                    'ORDID_27' => "R-$claim->consecutive",
                    'ORDDTE_27' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                    'FILL01A_27' => $so_master->FILL01A_27,
                    'FILL01_27' => $so_master->FILL01_27,
                    'SHPCDE_27' => $so_master->SHPCDE_27,
                    'REP1_27' => $so_master->REP1_27,
                    'SPLIT1_27' => $so_master->SPLIT1_27,
                    'REP2_27' => $so_master->REP2_27,
                    'SPLIT2_27' => $so_master->SPLIT2_27,
                    'REP3_27' => $so_master->REP3_27,
                    'SPLIT3_27' => $so_master->SPLIT3_27,
                    'COMMIS_27' => $so_master->COMMIS_27,
                    'TERMS_27' => $so_master->TERMS_27,
                    'SHPVIA_27' => $so_master->SHPVIA_27,
                    'XURR_27' => $so_master->XURR_27,
                    'FOB_27' => $so_master->FOB_27,
                    'TAXCD1_27' => $so_master->TAXCD1_27,
                    'TAXCD2_27' => $so_master->TAXCD2_27,
                    'TAXCD3_27' => $so_master->TAXCD3_27,
                    'COMNT1_27' => $so_master->COMNT1_27,
                    'COMNT2_27' => $so_master->COMNT2_27,
                    'COMNT3_27' => $so_master->COMNT3_27,
                    'SHPLBL_27' => $so_master->SHPLBL_27,
                    'INVCE_27' => $so_master->INVCE_27,
                    'APPINV_27' => $so_master->APPINV_27,
                    'REASON_27' => $claim->causes[0]->code,
                    'NAME_27' => $so_master->NAME_27,
                    'ADDR1_27' => $so_master->ADDR1_27,
                    'ADDR2_27' => $so_master->ADDR2_27,
                    'CITY_27' => $so_master->CITY_27,
                    'STATE_27' => $so_master->STATE_27,
                    'ZIPCD_27' => $so_master->ZIPCD_27,
                    'CNTRY_27' => $so_master->CNTRY_27,
                    'PHONE_27' => $so_master->PHONE_27,
                    'CNTCT_27' => $so_master->CNTCT_27,
                    'TAXPRV_27' => $so_master->TAXPRV_27,
                    'FEDTAX_27' => $so_master->FEDTAX_27,
                    'TAXABL_27' => $so_master->TAXABL_27,
                    'EXCRTE_27' => $so_master->EXCRTE_27,
                    'FIXVAR_27' => $so_master->FIXVAR_27,
                    'CURR_27' => $so_master->CURR_27,
                    'RCLDTE_27' => $so_master->RCLDTE_27,
                    'FILL02_27' => $so_master->FILL02_27,
                    'TTAX_27' => 0, /*empty*/
                    'LNETAX_27' => 'N',
                    'ADDR3_27' => $so_master->ADDR3_27,
                    'ADDR4_27' => $so_master->ADDR4_27,
                    'ADDR5_27' => $so_master->ADDR5_27,
                    'ADDR6_27' => $so_master->ADDR6_27,
                    'MCOMP_27' => $so_master->MCOMP_27,
                    'MSITE_27' => $so_master->MSITE_27,
                    'UDFKEY_27' => '', /*empty*/
                    'UDFREF_27' => '', /*empty*/
                    'SHPTHRU_27' => '', /*empty*/
                    'XDFINT_27' => 0,
                    'XDFFLT_27' => 0,
                    'XDFBOL_27' => '', /*empty*/
                    'XDFDTE_27' => null, /*empty*/
                    'XDFTXT_27' => '', /*empty*/
                    'FILLER_27' => '', /*empty*/
                    'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'BILLCDE_27' => '', /*empty*/
                ]);

            if ($claim->reason === 'discount' || $claim->reason === 'major-value') {
                DB::connection('MAX')
                    ->table('SO_Detail')
                    ->insert([
                        'ORDNUM_28' => $max_order_num,
                        'LINNUM_28' => '01',
                        'DELNUM_28' => '01',
                        'STATUS_28' => '3',
                        'CUSTID_28' => $claim->invoice->CLIENTE,
                        'PRTNUM_28' => $claim->reason === 'discount' ? 'O480100001' : 'O480100000',
                        'EDILIN_28' => '', /*empty*/
                        'TAXABL_28' => $claim->invoice->CODIVA === 'IVA-V19' ? 'Y' : 'N',
                        'GLXREF_28' => 61209505,
                        'CURDUE_28' => Carbon::parse($claim->invoice->VENCIMIENTO)->format('Y-m-d\Th:m:s.v'),
                        'QTLINE_28' => '', /*empty*/
                        'ORGDUE_28' => Carbon::parse($claim->invoice->VENCIMIENTO)->format('Y-m-d\Th:m:s.v'),
                        'QTDEL_28' => '', /*empty*/
                        'CUSDUE_28' => Carbon::parse($claim->invoice->VENCIMIENTO)->format('Y-m-d\Th:m:s.v'),
                        'PROBAB_28' => 0,
                        'SHPDTE_28' => null,  /*empty*/
                        'FILL04_28' => '', /*empty*/
                        'SLSUOM_28' => 'UN',
                        'REFRNC_28' => "{$claim->invoice->OV}0101",
                        'PRICE_28' => $claim->reason === 'discount' ? $claim->discount : $claim->major_value,
                        'ORGQTY_28' => 1,
                        'CURQTY_28' => 1,
                        'BCKQTY_28' => 0,
                        'SHPQTY_28' => 0,
                        'DUEQTY_28' => 1,
                        'INVQTY_28' => 0,
                        'DISC_28' => 0,
                        'STYPE_28' => 'CR',
                        'PRNT_28' => 'N',
                        'AKPRNT_28' => 'N',
                        'STK_28' => 'PROEST',
                        'COCFLG_28' => '', /*empty*/
                        'FORCUR_28' => $claim->reason === 'discount' ? $claim->discount : $claim->major_value,
                        'HSTAT_28' => 'R',
                        'SLSREP_28' => '', /*empty*/
                        'COMMIS_28' => 0,
                        'DRPSHP_28' => '', /*empty*/
                        'QUMQTY_28' => 0,
                        'TAXCDE1_28' => $claim->invoice->CODIVA,
                        'TAX1_28' => 0,
                        'TAXCDE2_28' => '', /*empty*/
                        'TAX2_28' => 0,
                        'TAXCDE3_28' => '', /*empty*/
                        'TAX3_28' => 0,
                        'MCOMP_28' => '', /*empty*/
                        'MSITE_28' => '', /*empty*/
                        'UDFKEY_28' => '',
                        'UDFREF_28' => '', /*empty*/
                        'DEXPFLG_28' => 'N',
                        'COST_28' => 0,
                        'MARKUP_28' => 0,
                        'QTORD_28' => '', /*empty*/
                        'XDFINT_28' => 0,
                        'XDFFLT_28' => 0,
                        'XDFBOL_28' => '', /*empty*/
                        'XDFDTE_28' => null,
                        'XDFTXT_28' => '', /*empty*/
                        'FILLER_28' => '', /*empty*/
                        'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                        'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                        'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'BOKDTE_28' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                        'DBKDTE_28' => null,
                        'REVLEV_28' => '', /*empty*/
                        'MANPRC_28' => 'N',
                        'ORGPRC_28' => $claim->reason === 'discount' ? $claim->discount : $claim->major_value,
                        'PRCALC_28' => 2,
                        'CLASS_28' => '', /*empty*/
                        'WARRES_28' => 0,
                        'JOB_28' => '', /*empty*/
                        'CSENDDTE_28' => null,
                        'CONSGND_28' => 0,
                        'CURCONSGND_28' => 0,
                        'CONSIGNSTK_28' => '', /*empty*/
                        'CURSHP_28' => 0,
                    ]);
            } else {
                foreach ($so_detail as $key => $detail) {
                    if ($claim->items->where('item', '=', trim("{$detail->LINNUM_28}{$detail->DELNUM_28}"))->count() > 0) {
                        $item = $claim->items->where('item', '=', trim("{$detail->LINNUM_28}{$detail->DELNUM_28}"))->first();

                        switch ($claim->reason) {
                            case 'quantity':
                                $item->quantity = $item->credit_note_quantity;
                                break;
                            case 'reposition':
                                $item->quantity = $item->reposition_quantity;
                                break;
                            case 'NA':
                                $item->quantity = $claim->action === 'return' ? $item->product->Cantidad : $item->delivered_quantity;
                                break;
                            case 'change':
                                $item->product_code = $item->new_product_code;
                                break;
                            default:
                                $item->quantity = $item->product->Cantidad;
                                break;
                        }

                        $linum = str_pad($key + 1, 2, 0, STR_PAD_LEFT);


                        DB::connection('MAX')
                            ->table('SO_Detail')
                            ->insert([
                                'ORDNUM_28' => $max_order_num,
                                'LINNUM_28' => $linum,
                                'DELNUM_28' => $detail->DELNUM_28,
                                'STATUS_28' => '3',
                                'CUSTID_28' => $claim->invoice->CLIENTE,
                                'PRTNUM_28' => $item->product_code,
                                'EDILIN_28' => '', /*empty*/
                                'TAXABL_28' => $detail->TAXABL_28,
                                'GLXREF_28' => 61209505,
                                'CURDUE_28' => Carbon::parse($detail->CURDUE_28)->format('Y-m-d\Th:m:s.v'),
                                'QTLINE_28' => '', /*empty*/
                                'ORGDUE_28' => Carbon::parse($detail->ORGDUE_28)->format('Y-m-d\Th:m:s.v'),
                                'QTDEL_28' => '', /*empty*/
                                'CUSDUE_28' => Carbon::parse($detail->CUSDUE_28)->format('Y-m-d\Th:m:s.v'),
                                'PROBAB_28' => 0,
                                'SHPDTE_28' => null,  /*empty*/
                                'FILL04_28' => '', /*empty*/
                                'SLSUOM_28' => 'UN',
                                'REFRNC_28' => $detail->REFRNC_28,
                                'PRICE_28' => $item->product->Precio,
                                'ORGQTY_28' => $item->quantity,
                                'CURQTY_28' => $item->quantity,
                                'BCKQTY_28' => 0,
                                'SHPQTY_28' => 0,
                                'DUEQTY_28' => $item->quantity,
                                'INVQTY_28' => 0,
                                'DISC_28' => 0,
                                'STYPE_28' => 'CR',
                                'PRNT_28' => 'N',
                                'AKPRNT_28' => 'N',
                                'STK_28' => $detail->STK_28,
                                'COCFLG_28' => '', /*empty*/
                                'FORCUR_28' => $item->product->Precio,
                                'HSTAT_28' => 'R',
                                'SLSREP_28' => '', /*empty*/
                                'COMMIS_28' => 0,
                                'DRPSHP_28' => '', /*empty*/
                                'QUMQTY_28' => 0,
                                'TAXCDE1_28' => $detail->TAXCDE1_28,
                                'TAX1_28' => 0,
                                'TAXCDE2_28' => '', /*empty*/
                                'TAX2_28' => 0,
                                'TAXCDE3_28' => '', /*empty*/
                                'TAX3_28' => 0,
                                'MCOMP_28' => '', /*empty*/
                                'MSITE_28' => '', /*empty*/
                                'UDFKEY_28' => '',
                                'UDFREF_28' => '', /*empty*/
                                'DEXPFLG_28' => 'N',
                                'COST_28' => $detail->COST_28,
                                'MARKUP_28' => 0,
                                'QTORD_28' => '', /*empty*/
                                'XDFINT_28' => 0,
                                'XDFFLT_28' => 0,
                                'XDFBOL_28' => '', /*empty*/
                                'XDFDTE_28' => null,
                                'XDFTXT_28' => '', /*empty*/
                                'FILLER_28' => '', /*empty*/
                                'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                                'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                                'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                                'BOKDTE_28' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                                'DBKDTE_28' => null,
                                'REVLEV_28' => '', /*empty*/
                                'MANPRC_28' => 'N',
                                'ORGPRC_28' => $item->product->Precio,
                                'PRCALC_28' => 2,
                                'CLASS_28' => '', /*empty*/
                                'WARRES_28' => 0,
                                'JOB_28' => '', /*empty*/
                                'CSENDDTE_28' => null,
                                'CONSGND_28' => 0,
                                'CURCONSGND_28' => 0,
                                'CONSIGNSTK_28' => '', /*empty*/
                                'CURSHP_28' => 0,
                            ]);

                        $so_detail_ext = DB::connection('MAX')
                            ->table('SO_Detail_Ext')
                            ->where('ORDER_LIN_DEL', '=', "{$ov}{$linum}{$detail->DELNUM_28}")
                            ->first();

                        if ($so_detail_ext) {
                            DB::connection('MAX')
                                ->table('SO_Detail_Ext')
                                ->insert([
                                    'ORDER_LIN_DEL' => "{$ov}{$linum}{$detail->DELNUM_28}",
                                    'ARTE' => $so_detail_ext->ARTE,
                                    'CodProdCliente' => $so_detail_ext->CodProdCliente,
                                    'Marca' => $so_detail_ext->Marca,
                                ]);
                        }
                    }
                }
            }

            DB::connection('MAX')->commit();

            return [
                'code' => 200,
                'msg' => 'successfully created',
                'id' => $max_order_num,
            ];
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return [
                'code' => 500,
                'msg' => "[{$e->getCode()} - {$e->getLine()}]: {$e->getMessage()}, FILE: {$e->getFile()}",
            ];
        }
    }

    /**
     * @param $claim
     * @return array
     */
    protected function remission($items, $customer_code, $claim_id, $seller_id): array
    {
        DB::beginTransaction();
        try {

            $remission = new RemissionHeader();
            $remission->customer_code = $customer_code;
            $remission->notes = 'REMISIÓN GENERADA DESDE LA APLICACIÓN DE RECLAMOS';
            $remission->type_sale = 'sale';
            $remission->state = '2';
            $remission->type_id = 3;
            $remission->created_by = Auth::id();
            $remission->bruto = array_sum(array_column($items, 'price')) * array_sum(array_column($items, 'quantity'));
            $remission->subtotal = array_sum(array_column($items, 'price')) * array_sum(array_column($items, 'quantity'));
            $remission->taxes = 0;
            $remission->discount = 0;
            $remission->currency = 'COP';
            $remission->claim_id = $claim_id;
            $remission->seller_id = $seller_id;
            $remission->save();

            foreach ($items as $item) {
                if ($item['quantity'] > 0){
                    $remission->detail()->create([
                        'product' => $item['product_code'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'unit_measurement' => 'units',
                        'art' => $item['art'],
                        'brand' => $item['brand'],
                        'notes' => $item['notes'],
                    ]);
                }
            }

            $remission->log()->create([
                'description' => 'Creo una remisión desde la aplicación de reclamos',
                'user_id' => Auth::id(),
            ]);


            DB::commit();

            return [
                'code' => 200,
                'msg' => 'successfully created',
                'id' => $remission->id,
                'consecutive' => $remission->consecutive,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'code' => 500,
                'msg' => "[{$e->getCode()} - {$e->getLine()}]: {$e->getMessage()}",
            ];
        }
    }

    /**
     * @param $claim
     * @return array
     * @throws Throwable
     */
    protected function sale_order($claim): array
    {
        DB::connection('MAX')->beginTransaction();
        try {
            $customer_code = $claim->reason == 'customer-change'
                ? $claim->new_customer_code
                : $claim->invoice->CLIENTE;

            $max_order_num = DB::connection('MAX')
                    ->table('SO_Master')
                    ->whereIn('STYPE_27', ['CU', 'CR'])
                    ->max('ORDNUM_27') + 1;

            $customer_master = CustomerMaster::where('CUSTID_23', '=', $customer_code)
                ->first();

            $subtotal = $claim->items->reduce(function ($a, $c) {
                return $a + ($c->price * $c->quantity);
            });

            DB::connection('MAX')
                ->table('SO_Master')
                ->insert([
                    'ORDNUM_27' => $max_order_num,
                    'CUSTID_27' => $customer_code,
                    'GLXREF_27' => '41209505',
                    'STYPE_27' => 'CU',
                    'STATUS_27' => '3',
                    'CUSTPO_27' => '',
                    'ORDID_27' => "R-$claim->consecutive",
                    'ORDDTE_27' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'FILL01A_27' => '', /*empty*/
                    'FILL01_27' => '', /*empty*/
                    'SHPCDE_27' => '', /*empty*/
                    'REP1_27' => trim($customer_master->SLSREP_23),
                    'SPLIT1_27' => 100,
                    'REP2_27' => '', /*empty*/
                    'SPLIT2_27' => 0,
                    'REP3_27' => '', /*empty*/
                    'SPLIT3_27' => 0,
                    'COMMIS_27' => trim($customer_master->COMMIS_23),
                    'TERMS_27' => trim($customer_master->TERMS_23),
                    'SHPVIA_27' => trim($customer_master->SHPVIA_23),
                    'XURR_27' => '', /*empty*/
                    'FOB_27' => trim($customer_master->CITY_23),
                    'TAXCD1_27' => $claim->invoice->CODIVA == 'IVA-V19' ? trim($customer_master->TXCDE1_23) : '',
                    'TAXCD2_27' => '', /*empty*/
                    'TAXCD3_27' => '', /*empty*/
                    'COMNT1_27' => '',
                    'COMNT2_27' => '',
                    'COMNT3_27' => '',
                    'SHPLBL_27' => 0,
                    'INVCE_27' => 'N',
                    'APPINV_27' => '', /*empty*/
                    'REASON_27' => '23',
                    'NAME_27' => trim($customer_master->NAME_23),
                    'ADDR1_27' => trim($customer_master->ADDR1_23),
                    'ADDR2_27' => trim($customer_master->ADDR2_23),
                    'CITY_27' => trim($customer_master->CITY_23),
                    'STATE_27' => trim($customer_master->STATE_23),
                    'ZIPCD_27' => trim($customer_master->ZIPCD_23),
                    'CNTRY_27' => trim($customer_master->CNTRY_23),
                    'PHONE_27' => trim($customer_master->PHONE_23),
                    'CNTCT_27' => trim($customer_master->CNTCT_23),
                    'TAXPRV_27' => trim($customer_master->TAXPRV_23),
                    'FEDTAX_27' => 'N',
                    'TAXABL_27' => $claim->invoice->CODIVA == 'IVA-V19' ? 'Y' : 'N',
                    'EXCRTE_27' => 1,
                    'FIXVAR_27' => 'V',
                    'CURR_27' => trim($customer_master->CURR_23),
                    'RCLDTE_27' => null,
                    'FILL02_27' => '', /*empty*/
                    'TTAX_27' => $subtotal * 0.19, /*empty*/
                    'LNETAX_27' => 'N',
                    'ADDR3_27' => trim($customer_master->ADDR3_23),
                    'ADDR4_27' => trim($customer_master->ADDR4_23),
                    'ADDR5_27' => trim($customer_master->ADDR5_23),
                    'ADDR6_27' => trim($customer_master->ADDR6_23),
                    'MCOMP_27' => trim($customer_master->MCOMP_23),
                    'MSITE_27' => trim($customer_master->MSITE_23),
                    'UDFKEY_27' => '', /*empty*/
                    'UDFREF_27' => '', /*empty*/
                    'SHPTHRU_27' => '', /*empty*/
                    'XDFINT_27' => 0,
                    'XDFFLT_27' => 0,
                    'XDFBOL_27' => '', /*empty*/
                    'XDFDTE_27' => null, /*empty*/
                    'XDFTXT_27' => '', /*empty*/
                    'FILLER_27' => '', /*empty*/
                    'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                    'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'BILLCDE_27' => '', /*empty*/
                ]);

            $flag = 1;
            foreach ($claim->items as $item) {
                switch ($claim->reason) {
                    case 'price':
                        $item->product->Precio = $item->new_price;
                        $item->quantity = $item->product->Cantidad;
                        break;
                    case 'quantity':
                    case 'quantity-new-invoice':
                        $item->quantity = $item->new_quantity;
                        break;
                    case 'reposition':
                        $item->quantity = $item->reposition_quantity;
                        break;
                    case 'NA':
                        $item->quantity = $claim->action === 'return' ? $item->product->Cantidad : $item->delivered_quantity;
                        break;
                    case 'change':
                    case 'product-change':
                        $item->product_code = $item->new_product_code;
                        break;
                    default:
                        $item->quantity = $item->product->Cantidad;
                        break;
                }

                $linum = str_pad($flag, 2, 0, STR_PAD_LEFT);

                $prtnum = DB::connection('MAX')
                    ->table('Part_Master')
                    ->where('PRTNUM_01', '=', $item->product_code)
                    ->first();

                $delivery_date = $this->calculate_delivery($prtnum->MFGLT_01);

                $cellar = DB::connection('MAX')
                    ->table('Part_Sales')
                    ->where('PRTNUM_29', '=', $item->product_code)
                    ->pluck('STK_29')
                    ->first();

                DB::connection('MAX')
                    ->table('SO_Detail')
                    ->insert([
                        'ORDNUM_28' => $max_order_num,
                        'LINNUM_28' => $linum,
                        'DELNUM_28' => '01',
                        'STATUS_28' => 3,
                        'CUSTID_28' => '',
                        'PRTNUM_28' => $item->product_code,
                        'EDILIN_28' => '', /*empty*/
                        'TAXABL_28' => $claim->invoice->CODIVA == 'IVA-V19' ? 'Y' : 'N',
                        'GLXREF_28' => 61209505,
                        'CURDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'), /*empty*/
                        'QTLINE_28' => '', /*empty*/
                        'ORGDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'QTDEL_28' => '', /*empty*/
                        'CUSDUE_28' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'PROBAB_28' => 0,
                        'SHPDTE_28' => null,  /*empty*/
                        'FILL04_28' => '', /*empty*/
                        'SLSUOM_28' => 'UN',
                        'REFRNC_28' => $max_order_num.$linum.'01',
                        'PRICE_28' => $item->product->Precio,
                        'ORGQTY_28' => $item->quantity,
                        'CURQTY_28' => $item->quantity,
                        'BCKQTY_28' => 0,
                        'SHPQTY_28' => 0,
                        'DUEQTY_28' => $item->quantity,
                        'INVQTY_28' => 0,
                        'DISC_28' => 0,
                        'STYPE_28' => 'CU',
                        'PRNT_28' => 'N',
                        'AKPRNT_28' => 'N',
                        'STK_28' => $cellar, /*empty*/
                        'COCFLG_28' => '', /*empty*/
                        'FORCUR_28' => $item->product->Precio,
                        'HSTAT_28' => 'R',
                        'SLSREP_28' => '', /*empty*/
                        'COMMIS_28' => 0,
                        'DRPSHP_28' => '', /*empty*/
                        'QUMQTY_28' => 0,
                        'TAXCDE1_28' => $claim->invoice->CODIVA == 'IVA-V19' ? $customer_master->TXCDE1_23 : '',
                        'TAX1_28' => $claim->invoice->CODIVA == 'IVA-V19' ? ($item->price * $item->quantity) * 0.19 : 0,
                        'TAXCDE2_28' => '', /*empty*/
                        'TAX2_28' => 0,
                        'TAXCDE3_28' => '', /*empty*/
                        'TAX3_28' => 0,
                        'MCOMP_28' => '', /*empty*/
                        'MSITE_28' => '', /*empty*/
                        'UDFKEY_28' => '', /*empty*/
                        'UDFREF_28' => '', /*empty*/
                        'DEXPFLG_28' => 'N',
                        'COST_28' => $prtnum->COST_01,
                        'MARKUP_28' => 0,
                        'QTORD_28' => '', /*empty*/
                        'XDFINT_28' => 0,
                        'XDFFLT_28' => 0,
                        'XDFBOL_28' => '', /*empty*/
                        'XDFDTE_28' => null,
                        'XDFTXT_28' => '', /*empty*/
                        'FILLER_28' => '', /*empty*/
                        'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                        'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                        'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'BOKDTE_28' => Carbon::now()->format('Y-m-d\T00:00:00.000'),
                        'DBKDTE_28' => null,
                        'REVLEV_28' => '', /*empty*/
                        'MANPRC_28' => 'N',
                        'ORGPRC_28' => $item->product->Precio,
                        'PRCALC_28' => 2,
                        'CLASS_28' => '', /*empty*/
                        'WARRES_28' => 0,
                        'JOB_28' => '', /*empty*/
                        'CSENDDTE_28' => null,
                        'CONSGND_28' => 0,
                        'CURCONSGND_28' => 0,
                        'CONSIGNSTK_28' => '', /*empty*/
                        'CURSHP_28' => 0,
                    ]);

                if ($item->art || $item->product->ARTE || $item->product->CodProdCliente || $item->product->Marca) {
                    DB::connection('MAX')
                        ->table('SO_Detail_Ext')
                        ->updateOrInsert([
                            'ORDER_LIN_DEL' => $max_order_num.$linum.'01',
                        ], [
                            'ARTE' => $item->product->ARTE,
                            'CodProdCliente' => $item->product->CodProdCliente,
                            'Marca' => $item->product->Marca,
                        ]);
                }

                DB::connection('MAX')
                    ->table('Order_Master')
                    ->insert([
                        'ORDNUM_10' => $max_order_num,
                        'LINNUM_10' => $linum,
                        'DELNUM_10' => '01',
                        'PRTNUM_10' => $item->product_code,
                        'CURDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'RECFLG_10' => 'N',
                        'TAXABLE_10' => 'N',
                        'TYPE_10' => 'CU',
                        'ORDER_10' => $max_order_num.$linum.'01',
                        'VENID_10' => '',  /*empty*/
                        'ORGDUE_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'PURUOM_10' => '',  /*empty*/
                        'CURQTY_10' => $item->quantity,
                        'ORGQTY_10' => $item->quantity,
                        'DUEQTY_10' => $item->quantity,
                        'CURPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'FILL03_10' => '', /*empty*/
                        'ORGPRM_10' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'FILL04_10' => '', /*empty*/
                        'FRMPLN_10' => 'Y',
                        'STATUS_10' => '3',
                        'STK_10' => $prtnum->DELSTK_01,
                        'CUSORD_10' => $max_order_num.$linum.'01',
                        'PLANID_10' => $prtnum->PLANID_01,
                        'BUYER_10' => $prtnum->BUYER_01,
                        'PSCRAP_10' => 0,
                        'ASCRAP_10' => 0,
                        'SCRPCD_10' => 'N',
                        'SCHCDE_10' => 'B',
                        'REVLEV_10' => '', /*empty*/
                        'COST_10' => $prtnum->COST_01,
                        'CSTCNV_10' => 1,
                        'APRDBY_10' => '', /*empty*/
                        'ORDREF_10' => $max_order_num.$linum.'01',
                        'TRNDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'FILL05_10' => '', /*empty*/
                        'SCHFLG_10' => 'R',
                        'CRTRAT_10' => '', /*empty*/
                        'NEGATV_10' => '', /*empty*/
                        'REQPEG_10' => '', /*empty*/
                        'MPNNUM_10' => '', /*empty*/
                        'LABOR_10' => 0,
                        'AMMEND_10' => 'N',
                        'LOTNUM_10' => '', /*empty*/
                        'BEGSER_10' => '', /*empty*/
                        'REWORK_10' => 'N',
                        'CRTSNS_10' => 'N',
                        'TTLSNS_10' => 0,
                        'FORCUR_10' => 0,
                        'EXCESS_10' => 0,
                        'UOMCST_10' => 0,
                        'UOMCNV_10' => 0,
                        'INSREQ_10' => '', /*empty*/
                        'CREDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'RTEREV_10' => '', /*empty*/
                        'RTEDTE_10' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'COMCDE_10' => '', /*empty*/
                        'ORDPTP_10' => '', /*empty*/
                        'JOBEXP_10' => '', /*empty*/
                        'JOBCST_10' => 0,
                        'TAXCDE_10' => '', /*empty*/
                        'TAX1_10' => 0,
                        'GLREF_10' => '', /*empty*/
                        'CURR_10' => '', /*empty*/
                        'UDFKEY_10' => '', /*empty*/
                        'UDFREF_10' => '', /*empty*/
                        'DISC_10' => 0,
                        'RECCOST_10' => 0,
                        'MPNMFG_10' => '', /*empty*/
                        'DEXPFLG_10' => 'N',
                        'PLSTPRNT_10' => 'N',
                        'ROUTPRNT_10' => 'N',
                        'REQUES_10' => '', /*empty*/
                        'CLSDTE_10' => null,
                        'XDFINT_10' => 0,
                        'XDFFLT_10' => 0,
                        'XDFBOL_10' => '', /*empty*/
                        'XDFDTE_10' => null,
                        'XDFTXT_10' => '', /*empty*/
                        'FILLER_10' => '', /*empty*/
                        'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                        'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                        'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'TSKCDE_10' => '', /*empty*/
                        'TSKTYP_10' => '', /*empty*/
                        'REPORTER_10' => '', /*empty*/
                        'PRIORITY_10' => '', /*empty*/
                        'PHONE_10' => '', /*empty*/
                        'LOCATION_10' => '', /*empty*/
                        'ALTBOM_10' => '', /*empty*/
                        'ALTRTG_10' => '', /*empty*/
                        'CLASS_10' => '', /*empty*/
                        'JOB_10' => '', /*empty*/
                        'SUBSHP_10' => 0,
                    ]);

                $qtycom = DB::connection('MAX')
                    ->table('Part_Sales')
                    ->where('PRTNUM_29', '=', $item->product_code)
                    ->pluck('QTYCOM_29')
                    ->first();

                DB::connection('MAX')
                    ->table('Part_Sales')
                    ->where('PRTNUM_29', '=', $item->product_code)
                    ->update([
                        'QTYCOM_29' => $qtycom + floatval($item->quantity),
                    ]);

                DB::connection('MAX')
                    ->table('Requirement_detail')
                    ->insert([
                        'ORDER_11' => $max_order_num.$linum.'01',
                        'PRTNUM_11' => $item->product_code,
                        'CURDUE_11' => Carbon::parse($delivery_date->DateValue)->format('Y-m-d\Th:m:s.v'),
                        'FILL01_11' => '',
                        'TYPE_11' => 'CU',
                        'ORDNUM_11' => $max_order_num,
                        'LINNUM_11' => $linum,
                        'DELNUM_11' => '01',
                        'CURQTY_11' => $item->quantity,
                        'ORGQTY_11' => $item->quantity,
                        'DUEQTY_11' => $item->quantity,
                        'STATUS_11' => '3',
                        'QTYPER_11' => '1',
                        'LTOSET_11' => '0',
                        'SCRAP_11' => '0',
                        'PICLIN_11' => '0',
                        'ISSQTY_11' => '0',
                        'REQREF_11' => $max_order_num.$linum.'01',
                        'ORDPEG_11' => '',
                        'ASCRAP_11' => '0',
                        'MCOMP_11' => '',
                        'MSITE_11' => '',
                        'UDFKEY_11' => '',
                        'UDFREF_11' => '',
                        'DEXPFLG_11' => '',
                        'XDFINT_11' => '0',
                        'XDFFLT_11' => '0',
                        'XDFBOL_11' => '',
                        'XDFDTE_11' => null,
                        'XDFTXT_11' => '',
                        'FILLER_11' => '',
                        'CreatedBy' => 'EVPIU-'.auth()->user()->username,
                        'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                        'ModifiedBy' => 'EVPIU-'.auth()->user()->username,
                        'ModificationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    ]);
                $flag++;
            }

            DB::connection('MAX')->commit();

            return [
                'code' => 200,
                'msg' => 'successfully created',
                'id' => $max_order_num,
            ];
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return [
                'code' => 500,
                'msg' => "[{$e->getCode()} - {$e->getLine()}]: {$e->getMessage()}",
            ];
        }
    }

    /**
     * @param $days
     * @return object
     */
    private function calculate_delivery($days): object
    {
        $business_days = DB::connection('MAX')
            ->table('Shop_Calendar')
            ->where('ShopDay', '=', 1)
            ->whereDate('DateValue', '>=', Carbon::now())
            ->get();

        if ($days > 0) {
            return $business_days[$days - 1];
        } else {
            $date = Carbon::now()->format('Y-m-d h:m:i');

            return (object) [
                'DateValue' => $date,
            ];
        }
    }
}
