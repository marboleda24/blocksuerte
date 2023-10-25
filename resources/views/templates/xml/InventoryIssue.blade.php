<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<eMAXExact>
    <MAX_Transaction_Table>
        <MAX_Transaction>
            <TYPE_39>I</TYPE_39>
            <SUBTYPE_39>U</SUBTYPE_39>
            <TNXDTE_39></TNXDTE_39> {{-- Fecha --}}
            <TNXTME_39></TNXTME_39> {{-- Hora --}}
            <PRTNUM_39>{{ trim($request->product_code) }}</PRTNUM_39>
            <ISSSTK_39>MPQUIM</ISSSTK_39>
            <LOCATOR_39></LOCATOR_39>
            <TNXQTY_39>{{ number_format($request->qty, 2, ',', '') }}</TNXQTY_39>
            <USERNAME_39>{{ auth()->user()->username }}</USERNAME_39>
            @if ($request->lots)
                <Lots>
                    @foreach($request->lots as $lot)
                        @if($lot['qty'] > 0)
                            <Lot>
                                <LOTNUM>{{ $lot['lot'] }}</LOTNUM>
                                <LOTQTY>{{ number_format($lot['qty'], 2, ',', '') }}</LOTQTY>
                                <EXPDTE></EXPDTE>
                                <QCODE>False</QCODE>
                                <DISPOSITION></DISPOSITION>
                            </Lot>
                        @endif
                    @endforeach
                </Lots>
            @endif
            <UDFKEY_39>EVPIU</UDFKEY_39>
            <REFDSC_39>{{$request->justify}}</REFDSC_39>
        </MAX_Transaction>
    </MAX_Transaction_Table>
</eMAXExact>
