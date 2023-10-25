<table width="100%">
    <tr>
        <td style="width: 25%;" class="text-center vertical-align-top">
            <div id="reference">
                <p style="font-weight: 700;"><strong>COMPROBANTE DE VACACIONES</strong></p>
                <p style="color: red;
                    font-weight: bold;
                    font-size: 18px;
                    margin-bottom: 8px;
                    padding: 5px 8px;
                    line-height: 1;
                    display: inline-block;">{{ $data->id }}</p>
                <p>FECHA DE CREACION: {{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}
            </div>
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div id="empresa-header">
                <strong>CI ESTRADA VELASQUEZ Y CIA. SAS</strong><br>
            </div>
            <div id="empresa-header1">
                CRA 55 29C 14 - Medellín - Antioquia - Colombia <br>
                Telefono - 2656665 <br>
                Correo electronico : info@estradavelasquez.com <br>
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img  style="width: 170px; height: auto; margin-top: -10px" src="{{ asset('img/evpiu_v2.png') }}" alt="logo">
        </td>
    </tr>
</table>
