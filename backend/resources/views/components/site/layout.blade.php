<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"@if ($htmlClass) class="{{ $htmlClass }}"@endif>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
{{-- Design: phones always render the 390px layout, scaled to the screen (unchanged from design/*.html). --}}
<script id="vp-lock">(function(){var m=document.querySelector('meta[name=viewport]');if(!m)return;function f(){var s=Math.min(screen.width,screen.height),p=!matchMedia('(orientation: landscape)').matches;m.setAttribute('content',s<=500&&p?'width=390, viewport-fit=cover':'width=device-width, initial-scale=1, viewport-fit=cover')}f();addEventListener('orientationchange',function(){setTimeout(f,80)})})()</script>
<meta name="theme-color" content="{{ $general->theme_color }}">
<title>{{ $documentTitle }}</title>
@if ($metaDescription)
<meta name="description" content="{{ $metaDescription }}">
@endif
@if ($noindex)
<meta name="robots" content="noindex">
@endif
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="{{ $attributes->get('og-type', 'website') }}">
<meta property="og:site_name" content="{{ $general->t('site_name') }}">
<meta property="og:title" content="{{ $documentTitle }}">
@if ($metaDescription)
<meta property="og:description" content="{{ $metaDescription }}">
@endif
@if ($socialImage)
<meta property="og:image" content="{{ $socialImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta property="og:locale" content="{{ app(\App\Settings\SeoSettings::class)->og_locale }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $documentTitle }}">
@if ($metaDescription)
<meta name="twitter:description" content="{{ $metaDescription }}">
@endif
@if ($socialImage)
<meta name="twitter:image" content="{{ $socialImage }}">
@endif
<link rel="icon" href="{{ \App\Support\Site\SiteMedia::url($general->favicon, 'svg/ornament.svg') }}">
<link rel="preload" href="{{ Vite::asset('resources/site/assets/fonts/Cairo-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>
@vite($stylesheets)
{{ $head ?? '' }}
</head>
<body class="{{ $bodyClass }}">
<a class="sr-only" href="#main">{{ __('site.skip_to_content') }}</a>

<x-site.header :crumb="$crumb" :logo="$logoUrl()" :items="$menus->get('header', collect())" />
<x-site.menu :logo="$logoUrl()" :items="$menus->get('header', collect())" />
{{-- Design "desktop-early": layout width without the scrollbar, smaller header after scrolling. Runs before first paint. --}}
<script>
(function () {
  var root = document.documentElement, header = document.querySelector('.site-header');
  function syncWidth() { root.style.setProperty('--layout-width', root.clientWidth + 'px'); }
  syncWidth();
  addEventListener('resize', syncWidth, { passive: true });
  if ('ResizeObserver' in window) new ResizeObserver(syncWidth).observe(root);
  if (header) { var onScroll = function () { header.classList.toggle('is-scrolled', scrollY > 40); }; addEventListener('scroll', onScroll, { passive: true }); onScroll(); }
})();
</script>

<main id="main" {{ $attributes->except('og-type') }}>
{{ $slot }}
</main>

<x-site.footer :general="$general" :logo="$logoUrl()" :socials="$socials"
    :columns="[$menus->get('footer_1', collect()), $menus->get('footer_2', collect())]" />

<script type="application/json" id="site-labels">@json(__('site.js'))</script>
@vite('resources/site/js/site.js')
{{ $scripts ?? '' }}
</body>
</html>
