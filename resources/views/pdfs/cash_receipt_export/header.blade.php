<div name="myheader">
    <table width="100%" style="border-bottom: 0.5px solid rgba(25,25,26,0.71);">
        <tr>
            <td width="10%">
                <img src="{{ public_path('img/logo_ev_new.png') }}" alt="logo" width="7%">
            </td>
            <td width="40%" style="color:#19191a; ">
                <span style="font-weight: bold; font-size: 14pt;">
                    CI ESTRADA VELASQUEZ & CIA SAS
                </span><br/>
                CR 55 29C 14 – ZONA INDUSTRIAL DE BELEN<br/>
                Medellin – Antioquia<br/>
                <span style="font-family:dejavusanscondensed;">&#9742;</span> (604)265-66-65
            </td>
            <td width="50%" style="text-align: right;">
                <span style="font-weight: bold; font-size: 14pt;">
                    RECIBO DE CAJA
                </span><br/>
                {{$data->consecutive}}<br/>
                {{ \Carbon\Carbon::now()->format('Y-m-d g:i A') }}
            </td>
        </tr>
    </table>
</div>
