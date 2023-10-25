<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>EVPIU</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="shortcut icon" type="image/png" href="{{ asset('/img/favicon_192x192.png') }}">
        <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/favicon_192x192.png') }}">

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        <noscript>
            <div class="container text-center">
                <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-700">
                    <strong class="text-white text-blod text-3xl">
                        [JAVASCRIPT ERROR]
                    </strong> <br>
                    <strong class="text-white text-2xl">We're sorry but EVPIU doesn't work properly without JavaScript enabled. Please enable it to continue. <br>
                    Lo sentimos, pero EVPIU no funciona correctamente sin JavaScript habilitado. Por favor, activalo para continuar.</strong>
                  </div>
            </div>
        </noscript>
    </head>
    <body class="app">
        @inertia
    </body>
</html>
