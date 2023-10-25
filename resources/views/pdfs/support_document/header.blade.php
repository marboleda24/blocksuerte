<table width="100%">
    <tr>
        <td style="width: 30%;" class="text-center vertical-align-top">
            <div id="reference" class="col-sm-12">
                <table class="table table-bordered table-condensed table-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 12px"><strong>DOCUMENTO SOPORTE</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="font-size: 12px">
                                <strong>{{$support_document->consecutive}}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-left">Fecha Creacion: {{$support_document->created_at->format('Y-m-d')}}</p>
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
                FPA-GVC-001
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img  style="width: 170px; height: auto; margin-top: -10px" src="{{ public_path('img/8909266178.png') }}" alt="logo">
        </td>
    </tr>
</table>
