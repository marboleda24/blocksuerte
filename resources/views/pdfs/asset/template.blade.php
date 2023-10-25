<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <tbody>
        <tr>
            <td width="25%"><b>CLASIFICACION</b></td>
            <td width="25%">{{ $asset->classification->name }} </td>
            <td width="25%"><b>ESTADO</b></td>
            <td width="25%">
                @if($asset->state === 'good')
                    <span style="color: green; font-weight: bold">
                        BUENO
                    </span>
                @elseif($asset->state === 'repair')
                    <span style="color: orange; font-weight: bold">
                        EN REPARACION
                    </span>
                @elseif($asset->state === 'discarded')
                    <span style="color: red; font-weight: bold">
                        DESCARTADO
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td width="25%"><b>PRIORIDAD</b></td>
            <td width="25%">
                @if($asset->priority === 'critical')
                    <span style="color: red; font-weight: bold">
                        BUENO
                    </span>
                @elseif($asset->priority === 'normal')
                    <span style="color: orange; font-weight: bold">
                        NORMAL
                    </span>
                @elseif($asset->priority === 'low')
                    <span style="color: green; font-weight: bold">
                        BAJA
                    </span>
                @endif
            </td>
            <td width="25%"><b>ULTIMA REVISION</b></td>
            <td width="25%">{{$asset->last_revision}}</td>
        </tr>
        <tr>
            <td width="25%"><b>CENTRO DE TRABAJO</b></td>
            <td width="25%">{{ $asset->work_center->name }}</td>
            <td width="25%"><b>CREADO POR</b></td>
            <td width="25%">{{ $asset->createdBy->name }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">MODELO</td>
            <td width="25%">{{ $asset->resume?->model }}</td>
            <td width="25%" style="font-weight: bold">VOLTAJE</td>
            <td width="25%">{{ $asset->resume?->voltage }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">MARCA</td>
            <td width="25%">{{ $asset->resume?->brand }}</td>
            <td width="25%" style="font-weight: bold">FRECUENCIA</td>
            <td width="25%">{{ $asset->resume?->frequency }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">POTENCIA</td>
            <td width="25%">{{ $asset->resume?->power }}</td>
            <td width="25%" style="font-weight: bold">WATTS</td>
            <td width="25%">{{ $asset->resume?->watts }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">AMPERAJE</td>
            <td width="25%">{{  $asset->resume?->amperage }}</td>
            <td width="25%" style="font-weight: bold">RPM</td>
            <td width="25%">{{ $asset->resume?->rpm }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">FRECUENCIA MANTENIMIENTO</td>
            <td width="25%">{{ $asset->resume?->maintenance_frequency }}</td>
            <td width="25%" style="font-weight: bold">DIMENSIONES</td>
            <td width="25%">{{ $asset->resume?->dimension }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">DESCRIPCION DE MANTIMIENTO PREVENTIVO</td>
            <td width="25%">{{ $asset->resume?->preventive_maintenance_description }}</td>
            <td width="25%" style="font-weight: bold">PRECUACIONES</td>
            <td width="25%">{{ $asset->resume?->precautions }}</td>
        </tr>

        <tr>
            <td width="25%" style="font-weight: bold">FUNCIONALIDAD</td>
            <td width="75%" colspan="3">{{ $asset->functionality }}</td>
        </tr>
    </tbody>
</table>

@foreach($asset->files as $file)
    <img src="data:image/png;base64, {{ $file->base64_file }}=" alt="{{ $file->name }}" width="50%"/>
@endforeach

<table class="items "  width="100%" style="font-size: 9pt; border-collapse: collapse; border: black ; margin-top:30px "  cellpadding="8" >
    <tbody>
    <tr>
        <td class="center" width="100%" style="font-weight: bold"  colspan="3">MANTENIMENTOS  </td>
    </tr>
    @foreach($asset?->maintenances->where('state', '=', '4') as $value)
    <tr>
        <td width="33%" style="font-weight: bold">CONSECUTIVO: {{$value->consecutive}}</td>
        <td width="33%" style="font-weight: bold">FECHA DE CIERRE: {{$value->closing_date}}</td>
        <td width="33%"  style="font-weight: bold">TIPO: {{$value->type_name }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
