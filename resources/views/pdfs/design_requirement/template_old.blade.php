<table width="100%">
    <tr>
        <td style="width: 65%; text-align: left; color: #555555; border-left: 6px solid #155e75" class="vertical-align-top">
            <div id="empresa-header" style="color: #1e293b; font-size: 1.4rem">
                <strong>ESPECIFICACIONES DE DISEÑO</strong><br>
            </div>
            <div id="empresa-header1" style="color: #475569; font-size: 1.2rem">
                <b>ARTICULO:</b> {{ $proposal->product }}<br>
                <b>TAMAÑO:</b> {{ $proposal->measure }}<br>
                <b>PESO(gr):</b> {{ $proposal->weight }}  <br>
                <b>MARCA:</b> {{ $proposal->proposal->header->brand->name }}
            </div>
        </td>
        <td style="width: 65%; text-align: left; color: #475569; border-left: 6px solid #155e75" class="text-center vertical-align-top">
            <div id="empresa-header" style="color: #1e293b; font-size: 1.4rem">
                <strong> REQUERIMIENTO #{{ $proposal->proposal->header->id }} - PROPUESTA #{{ $proposal->id }} - V{{ $proposal->version }}</strong><br>
            </div>
            <div id="empresa-header1" style="color: #475569; font-size: 1.2rem">
                <b>VENDEDOR(A): </b> {{ $proposal->proposal->header->seller->name }}<br>
                <b>MATERIAL: </b> {{ $proposal->proposal->header->material->material->name }} <br>
                <b>DISEÑADOR: </b> {{ $proposal->proposal->header->assigned_designer ? $proposal->proposal->header->assigned_designer->name : 'SIN ASIGNAR' }}<br>
                <b>FECHA: </b> {{ $proposal->created_at->format('Y-m-d h:m a') }}
            </div>
        </td>
    </tr>
</table>


<br>
<br>
<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th class="text-center" style="width: 50%; font-size: 1.2rem">DIBUJO 2D</th>
        <th class="text-center" style="width: 50%; font-size: 1.2rem">PLANO CON MEDIDAS</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">
                @if ($proposal->url2D)
                    <img src="{{ $proposal->url2D }}" alt="image_2D" style="width: 400px; height: 400px; object-fit: scale-down;">
                @endif
            </td>
            <td class="text-center">
                @if ($proposal->blueprint)
                    <img src="{{ $proposal->blueprint->miniature }}" alt="image_blueprint" style="width: 400px; height: 400px; object-fit: scale-down;">
                @endif
            </td>
        </tr>
    </tbody>
</table>

@if ($proposal->features_detail)
    <br>
    <table width="100%">
        <tr>
            <td style="width: 100%; text-align: left; color: #555555; border-left: 6px solid #155e75" class="vertical-align-top">
                <div id="empresa-header" style="color: #155e75; font-size: 14px">
                    <strong>CARACTERÍSTICAS:</strong><br>
                </div>
                <div id="empresa-header1" style="font-size: 12px">
                    <strong> {{ $proposal->features_detail }} </strong><br>
                </div>
            </td>
        </tr>
    </table>
@endif
