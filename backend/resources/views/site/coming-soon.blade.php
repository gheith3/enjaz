{{-- «قريبًا»: the only page visitors see while the site is locked before the launch (App\Http\Middleware\SiteLock).
     Standalone (no site layout). «دخول» opens the 6-digit code form (#sign, no script needed). --}}
@php
    $logo = file_get_contents(resource_path('site/assets/svg/logo.svg'));
    $gold = '<linearGradient id="cs-gold" x1="0" y1="0" x2="1" y2="1" gradientUnits="objectBoundingBox" spreadMethod="reflect">'
        .'<stop offset="0" stop-color="#A8843F"/><stop offset=".4" stop-color="#C9A866"/><stop offset=".5" stop-color="#E2C891"/><stop offset=".6" stop-color="#C9A866"/><stop offset="1" stop-color="#A8843F"/>'
        .'<animateTransform attributeName="gradientTransform" type="translate" values="-1 -1;1 1;-1 -1" dur="12s" repeatCount="indefinite"/></linearGradient>';
    $logo = str_replace(['<defs>', 'fill="#FFF"', 'id="a"', 'url(#a)', 'width="117" height="94" '],
        ['<defs>'.$gold, 'fill="url(#cs-gold)"', 'id="cs-clip"', 'url(#cs-clip)', 'class="cs-logo" role="img" aria-label="إنجازات" '], $logo);
    $fields = ['الرياضة', 'الأدب', 'الثقافة', 'العلوم', 'الفنون', 'الابتكار', 'ريادة الأعمال'];
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#04262A">
<title>{{ __('site.lock.title') }}</title>
<style>
@font-face{font-family:"CsText";src:url("{{ Vite::asset('resources/site/assets/fonts/Cairo-Variable.woff2') }}") format("woff2");font-weight:200 1000;font-display:swap}
@font-face{font-family:"CsRuqaa";src:url("{{ Vite::asset('resources/site/assets/fonts/ArefRuqaa-Bold-arabic.woff2') }}") format("woff2");font-weight:700;font-display:swap}
:root{--night:#04262A;--night2:#0A4146;--gold:#CDAA6A;--gold2:#E9D3A1;--mist:#BFD3D0;color-scheme:dark}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{font:400 16px/1.8 "CsText",system-ui,sans-serif;color:#EAF1EF;background:var(--night)}
.cs{min-height:100vh;display:flex;flex-direction:column;position:relative;overflow:hidden;
  background:radial-gradient(ellipse at 50% 38%,rgba(205,170,106,.16),transparent 55%),linear-gradient(180deg,var(--night2),var(--night) 75%)}
.cs::before{content:"";position:absolute;inset:0;background:radial-gradient(rgba(233,211,161,.10) 1px,transparent 1.4px) 0 0/26px 26px;pointer-events:none}
.cs-top{position:relative;display:flex;justify-content:flex-end;padding:22px max(24px,env(safe-area-inset-left,0px))}
.cs-sign{display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:700;color:var(--mist);border:1px solid rgba(205,170,106,.35);padding:6px 16px;border-radius:99px;text-decoration:none}
.cs-sign:hover{color:var(--gold2);border-color:var(--gold)}
.cs-sign svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2}
.cs-main{position:relative;flex:1;display:grid;place-items:center;text-align:center;padding:20px max(20px,env(safe-area-inset-left,0px)) 40px}
.cs-inner{max-width:760px}
.cs-mark{position:relative;width:min(230px,58vw);margin:0 auto 22px}
.cs-mark::before{content:"";position:absolute;inset:-30%;background:radial-gradient(circle,rgba(233,211,161,.10),transparent 65%);pointer-events:none}
.cs-logo{position:relative;display:block;width:100%;height:auto;filter:drop-shadow(0 0 8px rgba(233,211,161,.18))}
.cs-kick{margin:0;font:700 clamp(30px,6vw,56px)/1.45 "CsRuqaa",serif;color:var(--gold2);text-wrap:balance}
.cs-rule{display:flex;align-items:center;gap:14px;justify-content:center;margin:18px auto;color:var(--gold);font-weight:700;letter-spacing:4px;font-size:14px}
.cs-rule::before,.cs-rule::after{content:"";width:70px;height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.cs-title{margin:0;font-size:clamp(22px,3.4vw,30px);font-weight:800;color:#fff;text-wrap:balance}
.cs-title em{font-style:normal;color:var(--gold2)}
.cs-text{margin:14px auto 0;max-width:620px;color:var(--mist);font-size:17px;line-height:2}
.cs-fields{list-style:none;padding:0;margin:26px auto 0;display:flex;flex-wrap:wrap;justify-content:center;gap:8px 18px;color:var(--gold);font-weight:700;font-size:14px}
.cs-fields li+li::before{content:"◆";font-size:8px;margin-inline-end:18px;vertical-align:middle;opacity:.6}
.cs-foot{position:relative;text-align:center;font-size:13px;color:rgba(191,211,208,.6);padding:18px}
.cs-veil{position:fixed;inset:0;z-index:30;background:rgba(2,20,22,.72);display:none;place-items:center;padding:16px}
.cs-veil:target,.cs-veil.is-open{display:grid}
.cs-box{width:100%;max-width:380px;background:#FBF8F1;color:#16302F;border-radius:22px;padding:30px 26px;text-align:center;box-shadow:0 30px 60px -20px rgba(0,0,0,.6);border-top:4px solid var(--gold)}
.cs-box h2{margin:0 0 4px;font-size:21px}
.cs-box p{margin:0 0 18px;color:#5B6B69;font-size:14.5px}
.cs-pin{width:100%;font:700 26px/1 "CsText";letter-spacing:12px;text-align:center;padding:14px;border-radius:14px;border:1.5px solid #E2D6BF;background:#fff;color:#16302F;direction:ltr}
.cs-pin:focus{outline:none;border-color:#0C7F89;box-shadow:0 0 0 4px #E1F0EF}
.cs-err{color:#B4412F!important;font-size:14px!important;margin:8px 0 0!important}
.cs-btn{margin-top:14px;width:100%;font:inherit;font-weight:800;font-size:16px;color:#fff;background:#096770;border:0;border-radius:14px;padding:12px;cursor:pointer}
.cs-close{display:inline-block;margin-top:10px;color:#5B6B69;font-size:14px;text-decoration:none}
:focus-visible{outline:3px solid var(--gold);outline-offset:2px}
@media (prefers-reduced-motion:reduce){.cs-logo animateTransform{display:none}}
</style>
</head>
<body>
<div class="cs">
    <header class="cs-top">
        <a class="cs-sign" href="#sign"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>{{ __('site.lock.sign') }}</a>
    </header>
    <main class="cs-main"><div class="cs-inner">
        <div class="cs-mark">{!! $logo !!}</div>
        <p class="cs-kick">{{ __('site.lock.kicker') }}</p>
        <div class="cs-rule">{{ __('site.lock.soon') }}</div>
        <h1 class="cs-title">{{ __('site.lock.headline') }} <em>{{ __('site.lock.headline_em') }}</em></h1>
        <p class="cs-text">{{ __('site.lock.text') }}</p>
        <ul class="cs-fields">@foreach ($fields as $field)<li>{{ $field }}</li>@endforeach</ul>
    </div></main>
    <footer class="cs-foot">{{ __('site.lock.footer', ['year' => \App\Support\ArabicDate::digits(date('Y'))]) }}</footer>
</div>

<div class="cs-veil{{ $errors->has('code') ? ' is-open' : '' }}" id="sign">
    <form class="cs-box" method="post" action="{{ route('site.unlock') }}">
        @csrf
        <h2>{{ __('site.lock.form_title') }}</h2>
        <p>{{ __('site.lock.form_text') }}</p>
        <input class="cs-pin" name="code" type="password" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="off" required
               aria-label="{{ __('site.lock.form_title') }}" placeholder="••••••" autofocus>
        @error('code')<p class="cs-err">{{ $message }}</p>@enderror
        <button class="cs-btn" type="submit">{{ __('site.lock.sign') }}</button>
        <a class="cs-close" href="#">{{ __('site.lock.close') }}</a>
    </form>
</div>
</body>
</html>
