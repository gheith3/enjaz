<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->

    @livewireStyles
    <link href="{{asset("css/global.css")}}" rel="stylesheet">    
    <link href="{{asset("css/main.css")}}" rel="stylesheet">    
    @stack('styles')

</head>
<body dir="rtl">

<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" height="80" class="d-inline-block align-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">تواصل معنا</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">الإعلام</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">المؤسسات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">المنجزين</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">الإنجازات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">عنا</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="main">
    {{$slot}}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
@livewireScripts
@stack('scripts')

</body>
</html>
