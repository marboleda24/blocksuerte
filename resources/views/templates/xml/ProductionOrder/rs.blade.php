<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <MAX_Transaction_Table>
        <MAX_Transaction>
            <TYPE_39>R</TYPE_39>
            <SUBTYPE_39>S</SUBTYPE_39>
            <TNXDTE_39></TNXDTE_39> {{-- Fecha --}}
            <TNXTME_39></TNXTME_39> {{-- Hora --}}
            <ORDNUM_39>{{ $rs['op'] }}</ORDNUM_39> {{-- Numero de orden --}}
            <RCVSTK_39></RCVSTK_39>
            <TNXQTY_39>{{ $rs['quantity'] }}</TNXQTY_39>
            <REFDSC_39></REFDSC_39>
            <GLREF_39></GLREF_39>
            <USERNAME_39>{{ auth()->user()->username }}</USERNAME_39>
            <UDFKEY_39>EVPIU</UDFKEY_39>
            <UDFREF_39></UDFREF_39>
            <ASSCODE_39></ASSCODE_39>
            <LOT_39></LOT_39>
            <SERIAL_39></SERIAL_39>
            <QCODE_39></QCODE_39>
            <EXPDATE_39></EXPDATE_39>
            <DEFECT_39></DEFECT_39>
            <RECPL_39></RECPL_39>
            <DISPOSITION_39></DISPOSITION_39>
        </MAX_Transaction>
    </MAX_Transaction_Table>
</eMAXExact>
