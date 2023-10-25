<table width="100%">
    <tr>
        <td style="width: 30%;" class="text-center vertical-align-top">
            <div id="reference" class="col-sm-12">
                <table class="table table-bordered table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 12px;"><strong>REMISION DE VENTA #</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center" style="font-size: 12px">
                            <strong>{{ $invoice->NUMERO }}</strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="text-left">Fecha Generación: {{ $invoice->FECHA }}</p>
            </div>
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div id="empresa-header">
                <strong> {{ $entity === 'CIEV' ? 'CI ESTRADA VELASQUEZ Y CIA SAS' : 'PLASTICOS GOJA SAS' }} </strong><br>
            </div>
            <div id="empresa-header1">
                {{ $entity === 'CIEV' ? 'Somos autorretenedores de ICA según Res. 202050056223 de 24/09/2020' : 'Regimen comun no somos agentes de retencion de IVA' }}

                NIT: {{$entity === 'CIEV' ? '890926617-8' : '900349726-2'}} – Responsable de IVA

                {{ $entity === 'CIEV' ? 'CR 55 29C - 14' : 'CL 29D 55 - 88' }} | Medellin – Antioquia

                Teléfono: {{ $entity === 'CIEV' ? '(604) 265 6665' : '(604) 401 9044' }}

                Email: {{ $entity === 'CIEV' ? 'serviciosit@estradavelasquez.com' : 'produccion@platicosgoja.com' }}
            </div>
        </td>
        <td style="width: 30%; text-align: right;" class="vertical-align-top">
            <img  style="width: 100px; height: auto; margin-top: -10px" src="{{ $entity === 'CIEV' ? public_path('img/logo_ev_new.png') : public_path('img/goja_logo.png') }}" alt="logo">
        </td>
    </tr>
</table>
<br><br>
