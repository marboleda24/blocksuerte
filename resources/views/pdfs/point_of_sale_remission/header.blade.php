<table width="100%">
    <tr>
        <td style="width: 25%; border: #0f0f0f" class="text-center vertical-align-top">
            <div id="reference">
                <p style="font-weight: 700;"><strong>REMISIÓN POS</strong></p>
                <p style="color: red;
                    font-weight: bold;
                    font-size: 18px;
                    margin-bottom: 8px;
                    padding: 5px 8px;
                    line-height: 1;
                    display: inline-block;">{{ $remission->consecutive }}</p>
                <p>FECHA DE CREACIÓN <br> {{ $remission->created_at }}
            </div>
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div id="empresa-header">
                <strong>CI ESTRADA VELASQUEZ Y CIA. SAS</strong><br>
            </div>
            <div id="empresa-header1">
                CR 55 29C 14 - Medellin - Antioquia - Colombia <br>
                Teléfono - 2656665 <br>
                Correo electrónico: info@estradavelasquez.com <br>
                FPA-GVC-001
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img  style="width: 90px; height: auto; margin-top: -10px" src="{{ public_path('img/8909266178.jpg') }}" alt="logo">
        </td>
    </tr>
</table>
