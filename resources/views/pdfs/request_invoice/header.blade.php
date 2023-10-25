<table width="100%">
    <tr>
        <td style="width: 70%;" class="text-left vertical-align-top">
            <div id="reference">
                <p style="font-weight: 900;"><strong>RECLAMOS</strong></p>
                <p style="font-size: 12px"> FACTURA # {{ $details->invoice }}</p>
                <p class="text-center" style="font-size: 12px"> TICKET #{{$details->id}}</p>
                <p class="text-center" style="font-size: 12px"> MOTIVO: {{$details->reason_reprocessings->reason}}</p>
            </div>
        </td>

        <td style="width: 30%; text-align: right;" class="vertical-align-top">
            <img  style="width: 100px; height: auto; margin-top: -10px" src="{{ asset('img/ev_logo.png') }}" alt="logo">
        </td>
    </tr>
</table>
