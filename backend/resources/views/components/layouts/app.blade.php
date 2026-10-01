<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/aos.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/institutes.css') }}">

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    @livewireStyles
    {{--    <link href="{{asset("css/global.css")}}" rel="stylesheet">    --}}
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-pagination.css') }}" rel="stylesheet">
    @stack('styles')

</head>

<body dir="rtl">


    <div id="main">
        {{ $slot }}

        <livewire:components.footer />

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/aos.min.js') }}"></script>
    @livewireScripts
    @stack('scripts')

    <script>
        AOS.init();
    </script>

</body>

</html>
