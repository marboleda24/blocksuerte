<x-mail::message>
# AUDITORIA DIARIA DE FACTURACIÓN

<br>

@if($state)
EVPIU le informa que no se encontraron novedades en la facturación diaria del día **{!!\Carbon\Carbon::now()->subDay()->format('Y-m-d')!!}**
@else
EVPIU le informa que se encontraron diferencias en la facturación diaria del día **{!!\Carbon\Carbon::now()->subDay()->format('Y-m-d')!!}**, adjunto encontrará un PDF con la información detallada.
@endif

<br>

Gracias, <br>
Equipo de sistemas de Estrada velasquez
</x-mail::message>
