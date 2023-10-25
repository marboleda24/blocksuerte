<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<eMAXExact>
    <SO_Detail_Table>
        <SO_Detail>
            <ORDNUM_28>{{ $response_header->Message }}</ORDNUM_28>
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
            <REFRNC_28>{{ $response_header->Message.$linum.'01'  }}</REFRNC_28>
            <PRICE_28>{{ number_format($item->price, 2, '.', '') }}</PRICE_28>
            <ORGQTY_28>{{ $item->quantity }}</ORGQTY_28>
            <CURQTY_28>{{ $item->quantity }}</CURQTY_28>
            <BCKQTY_28>0</BCKQTY_28>
            <SHPQTY_28>0</SHPQTY_28>
            <CURSHP_28></CURSHP_28>
            <DUEQTY_28>{{ $item->quantity }}</DUEQTY_28>
            <INVQTY_28>0</INVQTY_28>
            <DISC_28>0</DISC_28>
            <STYPE_28>{{ $stype }}</STYPE_28>
            <PRNT_28>N</PRNT_28>
            <AKPRNT_28>N</AKPRNT_28>
            <STK_28>{{ $cellar }}</STK_28>
            <COCFLG_28></COCFLG_28>
            <FORCUR_28>{{ number_format($item->price, 2, '.', '') }}</FORCUR_28>
            <HSTAT_28>R</HSTAT_28>
            <SLSREP_28></SLSREP_28>
            <COMMIS_28>0</COMMIS_28>
            <DRPSHP_28></DRPSHP_28>
            <QUMQTY_28>0</QUMQTY_28>
            <TAXCDE1_28>{{ $order->taxable ? $order->customer_master->TXCDE1_23: '' }}</TAXCDE1_28>
            <TAX1_28>{{ $order->taxable ? (number_format($item->price, 2, '.', '') * $item->quantity) * 0.19 : 0 }}</TAX1_28>
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
            <ORGPRC_28>{{ number_format($item->price, 2, '.', '') }}</ORGPRC_28>
            <PRCALC_28>2</PRCALC_28>
            <CLASS_28></CLASS_28>
            <WARRES_28>0</WARRES_28>
            <JOB_28></JOB_28>
            <CSENDDTE_28></CSENDDTE_28>
            <CONSGND_28>0</CONSGND_28>
            <CURCONSGND_28>0</CURCONSGND_28>
            <CONSIGNSTK_28></CONSIGNSTK_28>
        </SO_Detail>
    </SO_Detail_Table>
</eMAXExact>
