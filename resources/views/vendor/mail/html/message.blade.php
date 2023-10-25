@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="https://epnwfa.stripocdn.email/content/guids/CABINET_11a1ff3e916ee1874eda83a19c3936fd/images/66971625532671387.png" alt="logo" width="70%">
<br>
NOTIFICACIONES EVPIU
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} Estrada Velasquez. <br>
Todos los derechos reservados
@endcomponent
@endslot
@endcomponent
