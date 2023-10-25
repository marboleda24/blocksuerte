<!DOCTYPE html>
<html lang="es">

<body>
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th>
                   Area Reproceso
                </th>
                <td>
                    {{$details->worksplace->name_workplaces}}
                </td>
            </tr>
            <tr>
                <th>
                    Observaciones Vendedor
                </th>
                <td>
                    {{$details->comments}}
                </td>
            </tr>
            <tr>
                <th>
                   Nombre Vendedor
                </th>
                <td>
                    {{$details->createdBy->name}}
                </td>
            </tr>
            <tr>
                @if($details->observations !== null)
                    <th> Observaciones Bodega</th>
                @endif
                @if($details->observations !== null)
                    <td class="text-left">{{$details->observations}}</td>
                @endif
            </tr>
            <tr>
                @if($details->observations_quality !== null)
                    <th> Observaciones Calidad</th>
                @endif
                @if($details->observations_quality !== null)
                    <td class="text-left">{{$details->observations_quality}}</td>
                @endif
            </tr>
            <tr>
                @if($details->justify !== null)
                    <th class="text-left">Justificación Reclamo Rechazado Calidad</th>
                @endif
                @if($details->justify !== null)
                    <td class="text-left">{{$details->justify}}</td>
                @endif
            </tr>
            <tr>
                @if($details->reopen_quality_comments !== null)
                    <th class="text-left">Reclamo reactivado calidad</th>
                @endif
                @if($details->reopen_quality_comments !== null)
                    <td class="text-left">{{$details->reopen_quality_comments}}</td>
                @endif
            </tr>
            <tr>
                @if($details->justify_send_store !== null)
                    <th class="text-left">Justificación enviar Reclamo de bodega a calidad</th>
                @endif
                @if($details->justify_send_store !== null)
                    <td class="text-left">{{$details->justify_send_store}}</td>
                @endif
            </tr>
            <tr>
                @if($details->justify_refuse_store !== null)
                    <th class="text-left">Justificación Reclamo Rechazado Bodega</th>
                @endif
                @if($details->justify_refuse_store !== null)
                    <td class="text-left">{{$details->justify_refuse_store}}</td>
                @endif
            </tr>
            <tr>
                @if($details->reopen_store_comments !== null)
                    <th class="text-left">Reclamo reactivado bodega</th>
                @endif
                @if($details->reopen_store_comments !== null)
                    <td class="text-left">{{$details->reopen_store_comments}}</td>
                @endif
            </tr>
        </thead>
    </table>
</body>
</html>
