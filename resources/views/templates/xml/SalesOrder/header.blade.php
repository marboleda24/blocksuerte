<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<eMAXExact>
    <SO_Master_Table>
        <SO_Master>
            <ORDNUM_27></ORDNUM_27>
            <CUSTID_27>{{ $order->customer_code }}</CUSTID_27>
            <GLXREF_27>41209505</GLXREF_27>
            <STYPE_27>{{ $stype }}</STYPE_27>
            <STATUS_27>3</STATUS_27>
            <CUSTPO_27>{{ $order->oc ?? '' }}</CUSTPO_27>
            <ORDID_27>{{ $order->id }}</ORDID_27>
            <ORDDTE_27>{{ \Carbon\Carbon::now()->format('Y-m-d 00:00:00') }}</ORDDTE_27>
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
            <COMNT1_27>{{ $notes['note_1'] }}</COMNT1_27>
            <COMNT2_27>{{ $notes['note_2'] }}</COMNT2_27>
            <COMNT3_27>{{ $notes['note_3'] }}</COMNT3_27>
            <SHPLBL_27></SHPLBL_27>
            <INVCE_27>N</INVCE_27>
            <APPINV_27></APPINV_27>
            <REASON_27>{{ $reason }}</REASON_27>
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
            <EXCRTE_27>{{ $excrte }}</EXCRTE_27>
            <FIXVAR_27>V</FIXVAR_27>
            <CURR_27>{{ $curr }}</CURR_27>
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
            <CreatedBy>{{ "EVPIU-".auth()->user()->username }}</CreatedBy>
            <ModifiedBy>{{ "EVPIU-".auth()->user()->username }}</ModifiedBy>
        </SO_Master>
    </SO_Master_Table>
</eMAXExact>
