<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <MAX_Transaction_Table>
        <MAX_Transaction>
            <TYPE_39>{{ $type }}</TYPE_39>
            <SUBTYPE_39>{{ $subtype }}</SUBTYPE_39>
            <TNXDTE_39></TNXDTE_39> {{-- Fecha --}}
            <TNXTME_39></TNXTME_39> {{-- Hora --}}
            <ORDNUM_39>{{ $row['oc'] }}</ORDNUM_39> {{-- Numero de orden --}}
            <LINNUM_39>{{ $row['line'] }}</LINNUM_39> {{-- linea --}}
            <DELNUM_39>{{ $row['item'] }}</DELNUM_39> {{--  --}}
            <OPRSEQ_39></OPRSEQ_39>
            <NXTOPR_39></NXTOPR_39>
            <PRTNUM_39>{{ $row['reference'] }}</PRTNUM_39>
            <ISSSTK_39></ISSSTK_39>
            <RCVSTK_39>{{ $row['cellar'] }}</RCVSTK_39>
            <LOCATOR_39></LOCATOR_39>
            <TNXQTY_39>{{ number_format($row['registry_quantity'], 2, ',', '') }}</TNXQTY_39>
            <REFDSC_39>{{ $row['invoice_reference'] }}</REFDSC_39>
            <GLREF_39></GLREF_39>
            <SHIFT_39></SHIFT_39>
            <EMPID_39></EMPID_39>
            <TICKET_39></TICKET_39>
            <STARTTIME_39></STARTTIME_39>
            <ENDTIME_39></ENDTIME_39>
            <SETUPTIME_39></SETUPTIME_39>
            <ELAPSED_39></ELAPSED_39>
            <RUNACT_39></RUNACT_39>
            <SETACT_39></SETACT_39>
            <ASCRAP_39></ASCRAP_39>
            <REASON_39></REASON_39>
            <USERNAME_39>{{ auth()->user()->username }}</USERNAME_39>
            <UDFKEY_39>EVPIU</UDFKEY_39>
            <UDFREF_39></UDFREF_39>
            <ASSCODE_39></ASSCODE_39>
            <LOT_39></LOT_39>
            <SERIAL_39></SERIAL_39>
            <TERMINAL_39></TERMINAL_39>
            <QCODE_39></QCODE_39>
            <EXPDATE_39></EXPDATE_39>
            <DEFECT_39></DEFECT_39>
            <RECPL_39></RECPL_39>
            <DISPOSITION_39></DISPOSITION_39>
            @if ($row['lots'])
                <Lots>
                    @foreach($row['lots'] as $lot)
                        <Lot>
                            <LOTNUM>{{ $lot['lot'] }}</LOTNUM>
                            <LOTQTY>{{ number_format($lot['quantity'], 2, ',', '') }}</LOTQTY>
                            <EXPDTE></EXPDTE>
                            <QCODE>False</QCODE>
                            <DISPOSITION></DISPOSITION>
                        </Lot>
                    @endforeach
                </Lots>
            @endif
        </MAX_Transaction>
    </MAX_Transaction_Table>
</eMAXExact>
