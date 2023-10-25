<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <MAX_Transaction_Table>
        <MAX_Transaction>
            <TYPE_39>P</TYPE_39>
            <SUBTYPE_39>C</SUBTYPE_39>
            <TNXDTE_39></TNXDTE_39>
            <TNXTME_39></TNXTME_39>
            <ORDNUM_39>{{ $request->op }}</ORDNUM_39>
            <OPRSEQ_39>{{ $request->seq }}</OPRSEQ_39>
            <TNXQTY_39>{{ $request->quantity }}</TNXQTY_39>
            <REFDSC_39>{{ $request->machine }}</REFDSC_39>
            <SHIFT_39>1</SHIFT_39>
            <RUNACT_39>0</RUNACT_39>
            <SETACT_39>0</SETACT_39>
            <USERNAME_39>{{ auth()->user()->username }}</USERNAME_39>
            <UDFKEY_39>EVPIU</UDFKEY_39>
            <UDFREF_39>EVPIU</UDFREF_39>
        </MAX_Transaction>
    </MAX_Transaction_Table>
</eMAXExact>
