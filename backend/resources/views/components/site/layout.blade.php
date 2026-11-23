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
{{-- تصاميم ٢٠٧٠ — ظهور الصفحة التالية: عند التنقل داخل الموقع تبقى الصفحة السابقة ثابتة حتى تجهز صورة البنر (أو صورة كبيرة في
     أول شاشة) بحدّ أقصى ٩٠٠ مللي ثانية، ثم تظهر الصفحة الجديدة كاملة بتلاشٍ ناعم، فلا تظهر صفحة باهتة/بيضاء بلا صورتها.
     حركات البنر تنتظر معها (لا تفوت بدايتها) ثم تعمل كما هي. --}}
<script id="x-reveal">(function () {
  try {
    if (!('onpagereveal' in window)) return;
    addEventListener('pagereveal', function (e) {
      var vt = e.viewTransition;
      if (!vt || !document.getAnimations || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      var d = document.documentElement, released = false, anims = [];
      var release = function () {
        if (released) return;
        released = true;
        d.classList.remove('x-hold');
        anims.forEach(function (a) { a.play(); });
      };
      d.classList.add('x-hold');
      setTimeout(release, 900);
      vt.ready.then(function () {
        if (released) return;
        anims = document.getAnimations().filter(function (a) { return a.effect && a.effect.pseudoElement === '::view-transition-new(root)'; });
        anims.forEach(function (a) { a.pause(); });
      }).catch(release);
      // الصفحة قد تُعرض قبل اكتمال قراءتها: نبحث عن صور أول شاشة الكبيرة بعد اكتمالها، وننتظر فكّ ترميزها.
      var check = function () {
        var vw = innerWidth, vh = innerHeight, min = vw * vh * 0.04, found = [], imgs = document.images;
        for (var i = 0; i < imgs.length; i++) {
          var r = imgs[i].getBoundingClientRect();
          var w = Math.min(r.right, vw) - Math.max(r.left, 0), h = Math.min(r.bottom, vh) - Math.max(r.top, 0);
          if (w > 0 && h > 0 && w * h >= min) found.push({ im: imgs[i], a: w * h });
        }
        found.sort(function (a, b) { return b.a - a.a; });
        Promise.all(found.slice(0, 4).map(function (f) { return f.im.decode ? f.im.decode().catch(function () {}) : 0; })).then(release);
      };
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', check); else check();
    });
  } catch (x) {}
})();</script>
{{-- تصاميم ٢٠٧٠ — سرعة التنقل: عند الوقوف على رابط (أو لمسه) تُجلب الصفحة مسبقًا (prefetch فقط، بلا تشغيل في الخلفية)، فتفتح فور الضغط. --}}
<script type="speculationrules">{"prefetch":[{"where":{"and":[{"href_matches":"/*"},{"not":{"href_matches":"/admin*"}},{"not":{"href_matches":"/livewire*"}},{"not":{"href_matches":"/horizon*"}},{"not":{"href_matches":"/mcp*"}},{"not":{"href_matches":"/oauth*"}},{"not":{"href_matches":"/*.pdf"}},{"not":{"selector_matches":"[target=_blank],[download],[data-no-prefetch]"}}]},"eagerness":"moderate"}]}</script>
<link rel="preload" href="{{ Vite::asset('resources/site/assets/fonts/Cairo-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>
@vite($stylesheets)
{{ $head ?? '' }}
</head>
<body class="{{ $bodyClass }}">
@if ($shell === 'about')
{{-- about.html: the menu sits before a .page wrapper that holds its own header, the section chips, main and footer. --}}
<x-site.menu :logo="$logoUrl()" :items="$menus->get('header', collect())" />
<div class="page">
<a class="sr-only" href="#main">{{ __('site.skip_to_content') }}</a>
<x-site.header :crumb="$crumb" :logo="$logoUrl()" :items="$menus->get('header', collect())" variant="about" />
{{ $beforeMain ?? '' }}
@else
<a class="sr-only" href="#main">{{ __('site.skip_to_content') }}</a>

<x-site.header :crumb="$crumb" :logo="$logoUrl()" :items="$menus->get('header', collect())" />
<x-site.menu :logo="$logoUrl()" :items="$menus->get('header', collect())" />
@endif
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
@if ($shell === 'about')
{{ $afterFooter ?? '' }}
</div>
{{ $afterPage ?? '' }}
@endif

<script type="application/json" id="site-labels">@json(__('site.js'))</script>
{{-- تصاميم ٢٠٧٠: ملف تنسيق كل قسم، يُجلب مسبقًا في وقت الفراغ وعند الوقوف على الرابط (core/prefetch.js) --}}
<script type="application/json" id="x-css-map">@json(\App\Support\Site\PagePrefetch::map())</script>
@vite('resources/site/js/site.js')
{{ $scripts ?? '' }}
</body>
</html>
