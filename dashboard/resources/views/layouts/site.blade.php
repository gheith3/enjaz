<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', __('site.meta.title'))</title>
@hasSection('meta')
    @yield('meta')
@else
    <meta name="description" content="{{ __('site.meta.description') }}">
    <meta property="og:title" content="@yield('title', __('site.meta.title'))">
    <meta property="og:description" content="{{ __('site.meta.description') }}">
    <meta property="og:type" content="website">
@endif
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="page">

@include('partials.nav')

{{ $slot ?? '' }}
@yield('content')

@hasSection('footer')
    @yield('footer')
@else
    @include('partials.footer')
@endif

</div>
</body>
</html>
