<div name="myheader">
    <table width="100%" style="border-bottom: 0.5px solid rgba(25,25,26,0.71);">
        <tr>
            <td width="10%">
                <img src="{{ public_path('img/logo_ev_new.png') }}" alt="logo" width="9%">
            </td>
            <td width="40%" style="color:#19191a; ">
                <span style="font-weight: bold; font-size: 11pt;">
                    CI ESTRADA VELASQUEZ & CIA SAS
                </span><br/>
                890926617-8 | Medellin – Antioquia<br/>
                CR 55 29C 14 – ZONA INDUSTRIAL DE BELEN<br/>
                <span style="font-family:dejavusanscondensed;">&#9742;</span> (604)265-66-65
            </td>
            <td width="50%" style="text-align: right;">
                <span style="font-weight: bold; font-size: 13pt;">
                    PEDIDO #{{ $order->consecutive }}
                </span><br/>
                {{ $order->created_at }} <br>
                {{ \Carbon\Carbon::now()->format('Y-m-d g:i A') }}
            </td>
        </tr>
    </table>
</div>
