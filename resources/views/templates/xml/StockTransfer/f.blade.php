<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <MAX_Transaction_Table>
        <MAX_Transaction>
            <TYPE_39>F</TYPE_39>
            <SUBTYPE_39></SUBTYPE_39>
            <TNXDTE_39></TNXDTE_39> {{-- Fecha --}}
            <TNXTME_39></TNXTME_39> {{-- Hora --}}
            <PRTNUM_39>{{ trim($item->product) }}</PRTNUM_39>
            <ISSSTK_39>{{ $item->from }}</ISSSTK_39>
            <RCVSTK_39>{{ $item->to }}</RCVSTK_39>
            <LOCATOR_39></LOCATOR_39>
            <TNXQTY_39>{{ $item->quantity }}</TNXQTY_39>
            <REFDSC_39></REFDSC_39>
            <GLREF_39></GLREF_39>
            <USERNAME_39>{{ auth()->user()->username }}</USERNAME_39>
            <UDFKEY_39>EVPIU</UDFKEY_39>
            <UDFREF_39></UDFREF_39>
            <ASSCODE_39></ASSCODE_39>
            @if(isset($item->lots))
                <Lots>
                    @foreach($item->lots as $lot)
                        <Lot>
                            <LOTNUM>{{ $lot->name }}</LOTNUM>
                            <LOTQTY>{{ $lot->quantity }}</LOTQTY>
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
