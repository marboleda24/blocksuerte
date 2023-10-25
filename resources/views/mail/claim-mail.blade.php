@component('mail::message')
Tienes un nuevo reclamo, <br>

Ingresa a la plataforma para ver tus reclamos pendientes

@component('mail::button', ['url' => 'http://192.168.1.44'])
Ingresar
@endcomponent

Saludos,<br>
Equipo de EVPIU
@endcomponent
