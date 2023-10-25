<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <SO_Master_Table>
        <SO_Master>
            <ORDNUM_27></ORDNUM_27>
            <CUSTID_27>{{ $order->customer_code }}</CUSTID_27>
            <GLXREF_27></GLXREF_27>
            <STYPE_27>CU</STYPE_27>
            <STATUS_27>3</STATUS_27>
            <CUSTPO_27>{{ $order->oc ?? '' }}</CUSTPO_27>
            <ORDID_27>{{ $order->id }}</ORDID_27>
            <ORDDTE_27></ORDDTE_27>
            <SHPCDE_27></SHPCDE_27>
            <REP1_27>{{ trim($order->customer_master->SLSREP_23) }}</REP1_27>
            <SPLIT1_27>100</SPLIT1_27>
            <REP2_27></REP2_27>
            <SPLIT2_27></SPLIT2_27>
            <REP3_27></REP3_27>
            <SPLIT3_27></SPLIT3_27>
            <COMMIS_27>{{ trim($order->customer_master->COMMIS_23) }}</COMMIS_27>
            <TERMS_27>{{ trim($order->customer_master->TERMS_23) }}</TERMS_27>
            <SHPVIA_27>{{ trim($order->customer_master->SHPVIA_23) }}</SHPVIA_27>
            <XURR_27></XURR_27>
            <FOB_27>{{ trim($order->customer_master->CITY_23) }}</FOB_27>
            <TAXCD1_27>{{ $order->taxable ? trim($order->customer_master->TXCDE1_23) : '' }}</TAXCD1_27>
            <TAXCD2_27></TAXCD2_27>
            <TAXCD3_27></TAXCD3_27>
            <COMNT1_27>{{ $note_1 }}</COMNT1_27>
            <COMNT2_27>{{ $note_2 }}</COMNT2_27>
            <COMNT3_27>{{ $note_3 }}</COMNT3_27>
            <SHPLBL_27></SHPLBL_27>
            <INVCE_27>N</INVCE_27>
            <APPINV_27></APPINV_27>
            <REASON_27>{{ $order->destiny == 'C' ? '23' : ($order->destiny == 'P' ? '22' : $request->dies_reason) }}</REASON_27>
            <NAME_27>{{ trim($order->customer_master->NAME_23) }}</NAME_27>
            <ADDR1_27>{{ trim($order->customer_master->ADDR1_23) }}</ADDR1_27>
            <ADDR2_27>{{ trim($order->customer_master->ADDR2_23) }}</ADDR2_27>
            <CITY_27>{{ trim($order->customer_master->CITY_23) }}</CITY_27>
            <STATE_27>{{ trim($order->customer_master->STATE_23) }}</STATE_27>
            <ZIPCD_27>{{ trim($order->customer_master->ZIPCD_23) }}</ZIPCD_27>
            <CNTRY_27>{{ trim($order->customer_master->CNTRY_23) }}</CNTRY_27>
            <PHONE_27>{{ trim($order->customer_master->PHONE_23) }}</PHONE_27>
            <CNTCT_27>{{ trim($order->customer_master->CNTCT_23) }}</CNTCT_27>
            <TAXPRV_27>{{ trim($order->customer_master->TAXPRV_23) }}</TAXPRV_27>
            <FEDTAX_27>N</FEDTAX_27>
            <TAXABL_27>{{ $order->taxable === 1 ? 'Y' : 'N' }}</TAXABL_27>
            <EXCRTE_27>1</EXCRTE_27>
            <FIXVAR_27>V</FIXVAR_27>
            <CURR_27>{{ trim($order->customer_master->CURR_23) }}</CURR_27>
            <RCLDTE_27></RCLDTE_27>
            <TTAX_27>{{ $order->taxes }}</TTAX_27>
            <LNETAX_27>N</LNETAX_27>
            <ADDR3_27>{{ trim($order->customer_master->ADDR3_23) }}</ADDR3_27>
            <ADDR4_27>{{ trim($order->customer_master->ADDR4_23) }}</ADDR4_27>
            <ADDR5_27>{{ trim($order->customer_master->ADDR5_23) }}</ADDR5_27>
            <ADDR6_27>{{ trim($order->customer_master->ADDR6_23) }}</ADDR6_27>
            <MCOMP_27>{{ trim($order->customer_master->MCOMP_23) }}</MCOMP_27>
            <MSITE_27>{{ trim($order->customer_master->MSITE_23) }}</MSITE_27>
            <UDFKEY_27></UDFKEY_27>
            <UDFREF_27></UDFREF_27>
            <SHPTHRU_27></SHPTHRU_27>
            <FILL01A_27></FILL01A_27>
            <FILL01_27></FILL01_27>
            <FILL02_27></FILL02_27>
            <FILLER_27></FILLER_27>
            <BILLCDE_27></BILLCDE_27>
            <CreatedBy>{{ "EVPIU".auth()->user()->username }}</CreatedBy>
            <ModifiedBy>{{ "EVPIU".auth()->user()->username }}</ModifiedBy>

            @foreach($order->details as $idx => $item)
                @php
                    $linum = str_pad($idx + 1, 2, 0, STR_PAD_LEFT);
                    $prtnum = DB::connection('MAX')->table('Part_Master')->where('PRTNUM_01', '=', $item->product)->first();
                    $delivery_date = calculateDelivery($prtnum->MFGLT_01);
                    $cellar = DB::connection('MAX')->table('Part_Sales')->where('PRTNUM_29', '=', $item->product)->pluck('STK_29')->first();

                @endphp

                <SO_Detail>
                    <ORDNUM_28></ORDNUM_28>
                    <LINNUM_28>{{ $linum }}</LINNUM_28>
                    <DELNUM_28>01</DELNUM_28>
                    <STATUS_28>3</STATUS_28>
                    <CUSTID_28>{{ $order->customer_code }}</CUSTID_28>
                    <PRTNUM_28>{{ $item->product }}</PRTNUM_28>
                    <EDILIN_28></EDILIN_28>
                    <TAXABL_28>{{ $order->taxable == 1 ? 'Y' : 'N' }}</TAXABL_28>
                    <GLXREF_28>61209505</GLXREF_28>
                    <CURDUE_28>{{ $delivery_date->DateValue }}</CURDUE_28>
                    <QTLINE_28></QTLINE_28>
                    <ORGDUE_28>{{ $delivery_date->DateValue }}</ORGDUE_28>
                    <QTDEL_28></QTDEL_28>
                    <CUSDUE_28>{{ $delivery_date->DateValue }}</CUSDUE_28>
                    <PROBAB_28>0</PROBAB_28>
                    <SHPDTE_28></SHPDTE_28>
                    <SLSUOM_28>UN</SLSUOM_28>
                    <REFRNC_28></REFRNC_28>
                    <PRICE_28>{{ $item->price }}</PRICE_28>
                    <ORGQTY_28>{{ $item->quantity }}</ORGQTY_28>
                    <CURQTY_28>{{ $item->quantity }}</CURQTY_28>
                    <BCKQTY_28>0</BCKQTY_28>
                    <SHPQTY_28>0</SHPQTY_28>
                    <CURSHP_28></CURSHP_28>
                    <DUEQTY_28>{{ $item->quantity }}</DUEQTY_28>
                    <INVQTY_28>0</INVQTY_28>
                    <DISC_28>0</DISC_28>
                    <STYPE_28>CU</STYPE_28>
                    <PRNT_28>N</PRNT_28>
                    <AKPRNT_28>N</AKPRNT_28>
                    <STK_28>{{ $cellar }}</STK_28>
                    <COCFLG_28></COCFLG_28>
                    <FORCUR_28>{{ $item->price }}</FORCUR_28>
                    <HSTAT_28>R</HSTAT_28>
                    <SLSREP_28></SLSREP_28>
                    <COMMIS_28>0</COMMIS_28>
                    <DRPSHP_28></DRPSHP_28>
                    <QUMQTY_28>0</QUMQTY_28>
                    <TAXCDE1_28>{{ $order->taxable ? $order->customer_master->TXCDE1_23: '' }}</TAXCDE1_28>
                    <TAX1_28>{{ $order->taxable ? ($item->price * $item->quantity) * 0.19 : 0 }}</TAX1_28>
                    <TAXCDE2_28></TAXCDE2_28>
                    <TAX2_28>0</TAX2_28>
                    <TAXCDE3_28></TAXCDE3_28>
                    <TAX3_28>0</TAX3_28>
                    <MCOMP_28></MCOMP_28>
                    <MSITE_28></MSITE_28>
                    <UDFKEY_28>{{ $item->type === 'new' ? 'N': '' }}</UDFKEY_28>
                    <UDFREF_28></UDFREF_28>
                    <DEXPFLG_28></DEXPFLG_28>
                    <COST_28>{{ $prtnum->COST_01 }}</COST_28>
                    <MARKUP_28>0</MARKUP_28>
                    <QTORD_28></QTORD_28>
                    <FILLER_28></FILLER_28>
                    <CreatedBy>{{ "EVPIU-". auth()->user()->username }}</CreatedBy>
                    <ModifiedBy>{{ "EVPIU-". auth()->user()->username }}</ModifiedBy>
                    <BOKDTE_28></BOKDTE_28>
                    <DBKDTE_28></DBKDTE_28>
                    <REVLEV_28></REVLEV_28>
                    <MANPRC_28>N</MANPRC_28>
                    <ORGPRC_28>{{ $item->price }}</ORGPRC_28>
                    <PRCALC_28>2</PRCALC_28>
                    <CLASS_28></CLASS_28>
                    <WARRES_28>0</WARRES_28>
                    <JOB_28></JOB_28>
                    <CSENDDTE_28></CSENDDTE_28>
                    <CONSGND_28>0</CONSGND_28>
                    <CURCONSGND_28>0</CURCONSGND_28>
                    <CONSIGNSTK_28></CONSIGNSTK_28>
                </SO_Detail>
            @endforeach
        </SO_Master>
    </SO_Master_Table>
</eMAXExact>
