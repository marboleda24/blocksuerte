@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
           Notificaciones {{ config('app.name') }}
        @endcomponent
    @endslot

    ![Logo de Programación y más][logo]

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
            © {{ date('Y') }} Estrada Velasquez.
            Todos los derechos reservados.
        @endcomponent
    @endslot

    [logo]: https://programacionymas.com/images/mago/mago-200x200.png
@endcomponent
