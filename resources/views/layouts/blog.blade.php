<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="shortcut icon" type="image/png" href="{{ asset('/img/favicon_192x192.png') }}">
        <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/favicon_192x192.png') }}">
        <title>{{ config('app.name') }}</title>
        <!-- tailwind core CSS -->
        @vite('resources/dist/css/app.css')
        <!--Replace with your tailwind.css once created-->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        @stack('styles')
    </head>

    <body class="text-gray-800 antialiased bg-white p-0">
        <nav class="top-0 absolute z-50 w-full flex flex-wrap items-center justify-between px-2 py-3 navbar-expand-lg">
            <div class="container px-4 mx-auto flex flex-wrap items-center justify-between">
                <div class="lg:flex flex-grow items-center bg-white lg:bg-transparent lg:shadow-none hidden" id="example-collapse-navbar">
                    <ul class="relative flex-col lg:flex-row list-none lg:mr-auto">
                        <li class="static items-center">
                            <img src="{{asset('img/favicon_192x192.png')}}" alt="logo" style="width: 25%">
                        </li>
                    </ul>
                </div>
                <div class="lg:flex flex-grow items-center bg-white lg:bg-transparent lg:shadow-none hidden" id="example-collapse-navbar">
                    <ul class="flex flex-col lg:flex-row list-none lg:ml-auto">
                        <li class="flex items-center">
                            @if (Route::has('login'))
                                @auth
                                    <a class="text-white background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" href="{{ url('/dashboard') }}" style="transition: all .15s ease">
                                        <i class="fas fa-home"></i> Plataforma
                                    </a>
                                @else
                                    <a class="text-white background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" href="{{ url('/login') }}" style="transition: all .15s ease">
                                        <i class="fas fa-sign-in-alt"></i> Iniciar sesion
                                    </a>
                                @endauth
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <div class="relative flex content-center items-center justify-center" style="min-height: 10vh;">
                <div class="absolute top-0 w-full h-full bg-center bg-cover" style="background-image:url('/img/home_bg.JPG');">
                    <span id="blackOverlay" class="w-full h-full absolute opacity-75 bg-black"></span>
                </div>
                <div class="container relative mx-auto">
                    <div class="items-center flex flex-wrap">
                        <div class="w-full lg:w-6/12 px-4 ml-auto mr-auto text-center justify-center items-center">
                            <h1 class="text-white font-semibold text-5xl">
                                EVPIU
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="pt-12 pb-48">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center text-center mb-12">
                        <div class="w-full lg:w-full px-4">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative bg-gray-300 pt-8 pb-6">
            <div class="bottom-auto top-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden -mt-20" style="height: 80px; transform: translateZ(0px);">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                    <polygon class="text-gray-300 fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
            <div class="container mx-auto px-4">
                <hr class="my-6 border-gray-400">
                <div class="flex flex-wrap items-center md:justify-between justify-center">
                    <div class="w-full md:w-4/12 px-4 mx-auto text-center">
                        <div class="text-sm text-gray-600 font-semibold py-1">
                            <p class="copyright text-muted">Copyright &copy; <script>var d = new Date(); document.write(d.getFullYear());</script> <a href="https://estradavelasquez.com/">Estrada Velasquez</a>. <br> Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        @stack('javascript')
    </body>
</html>
