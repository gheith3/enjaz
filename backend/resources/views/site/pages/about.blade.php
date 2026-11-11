{{-- Design: about.html. The page script (resources/site/js/sections/design/about-page.js, from the design's inline script)
     builds the field ring, roadmap, testimonials, team, achievements and media kit from window.INJAZ_ABOUT (printed in
     site.sections.about.parts from the page's sections and their items). Hero and «الرسالة والرؤية والقيم» are rendered
     here; their art is unchanged. --}}
@php
    $p = $page;
    $em = fn ($t) => collect(explode('|', (string) $t))->map(fn ($x, $i) => $i % 2 ? '<em>'.e($x).'</em>' : e($x))->implode('');
@endphp
<x-site.layout :page="$p" styles="about" body-class="about-page" shell="about"
    :crumb="['title' => $p->label('crumb', $p->title), 'url' => null, 'sub' => null]">
  <x-slot:beforeMain><nav class="chips-nav" aria-label="{{ __('site.about.sections') }}"><ul data-chips></ul></nav></x-slot:beforeMain>

<section class="hero wrap" id="top" aria-label="{{ $p->title }}">
  <div class="hero-pattern" aria-hidden="true"></div>
  <div class="hero-copy">
    <span class="ab-kick ab-dk">{{ $p->hero_eyebrow ?: $p->title }}</span>
    <h1 class="ab-title ab-dk">{!! $em($p->hero_title ?: __('site.about.title')) !!}</h1>
    @if ($p->hero_lead)<p class="hero-lead">{{ $p->hero_lead }}</p>@endif
    <ul class="ab-facts ab-dk" aria-label="{{ __('site.about.facts') }}">@foreach ([1, 2, 3] as $i)@if ($n = $p->label('fact'.$i.'_n'))<li><b>{{ $n }}</b><span>{{ $p->label('fact'.$i.'_t') }}</span></li>@endif @endforeach</ul>
    <div class="ab-acts ab-dk"><a class="ab-btn" href="#journey">{{ $p->label('journey_button', __('site.about.journey_button')) }} <span aria-hidden="true">←</span></a><a class="ab-btn ab-ghost" href="{{ $p->hero_cta_url ?: url('/contact') }}">{{ $p->label('contact_button', __('site.about.contact_button')) }}</a></div>
  </div>
  <div class="hero-art" data-tilt>
    <div class="ring" aria-hidden="true"></div><div class="ring r2" aria-hidden="true"></div>
    <span class="spark" style="top:12%;right:18%" aria-hidden="true"></span>
    <span class="spark" style="top:40%;left:6%" aria-hidden="true"></span>
    <span class="spark" style="bottom:22%;right:8%" aria-hidden="true"></span>
    <div class="hero-image-blend">@if ($heroImg = $p->getFirstMedia('cutout'))<img src="{{ $heroImg->getUrl() }}" fetchpriority="high" alt="{{ $heroImg->getCustomProperty('alt') }}" width="1199" height="1312">@endif</div>
  </div>
</section>

<section class="vmv" id="mission" aria-label="{{ __('site.about.vmv') }}">
  <div class="vmv-stars" aria-hidden="true" data-vmv-stars></div>
  <div class="vmv-deck" data-vmv-deck>
    <article class="vmv-card" id="vmv-mission" aria-label="{{ __('site.about.mission_title') }}">
      <span class="vmv-badge"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/></svg></span>
      <h3>{{ __('site.about.mission_title') }}</h3><span class="vmv-rule" aria-hidden="true"></span>
      <p class="lead">{{ $p->label('mission') }}</p>
      <div class="vmv-art art-mission" aria-hidden="true">
        <span class="m-beam"></span><span class="m-tile" style="left:6%;top:74px;--r:-8deg;animation-delay:0s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20v-8M10 20V5M16 20v-11M22 20H2"/></svg><b>إحصاء</b></span><span class="m-tile" style="left:27%;top:24px;--r:5deg;animation-delay:-1.2s"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6M15.5 14.5c3 0 5.5 2 5.5 5"/></svg><b>أثر</b></span><span class="m-tile" style="left:51%;top:24px;--r:-5deg;animation-delay:-2.4s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21v-9M12 12c0-4 3-7 8-7 0 5-3 7-8 7zM12 14c0-3-2-6-7-6 0 4 3 6 7 6z"/></svg><b>نمو</b></span><span class="m-tile" style="left:72%;top:74px;--r:8deg;animation-delay:-3.6s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18h8M3 22h18M14 22a7 7 0 0 0 0-14h-1M9 14h2M9 12a2 2 0 0 1-2-2V6h6v4a2 2 0 0 1-2 2zM12 6V3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3"/></svg><b>علم</b></span><span class="m-glow"></span><span class="m-parts" data-vmv-parts></span>
        <svg class="m-book" viewBox="0 0 300 120"><defs>
          <linearGradient id="pgL" x1="0" x2="1"><stop offset="0" stop-color="#C9B48A"/><stop offset="1" stop-color="#FFF5DC"/></linearGradient>
          <linearGradient id="pgR" x1="1" x2="0"><stop offset="0" stop-color="#C9B48A"/><stop offset="1" stop-color="#FFF5DC"/></linearGradient></defs>
          <path d="M150 112 C110 94 50 94 10 106 L10 116 C50 104 110 104 150 120 C190 104 250 104 290 116 L290 106 C250 94 190 94 150 112Z" fill="#7A5A28"/>
          <path d="M150 44 C112 22 52 24 14 36 L14 106 C52 94 112 94 150 112Z" fill="url(#pgL)"/>
          <path d="M150 44 C188 22 248 24 286 36 L286 106 C248 94 188 94 150 112Z" fill="url(#pgR)"/>
          <g stroke="#A8916A" stroke-width=".8" opacity=".5" fill="none"><path d="M30 50 C70 40 110 42 138 56M30 62 C70 52 110 54 138 68M30 74 C70 64 110 66 138 80M30 86 C70 76 110 78 138 92"/><path d="M270 50 C230 40 190 42 162 56M270 62 C230 52 190 54 162 68M270 74 C230 64 190 66 162 80M270 86 C230 76 190 78 162 92"/></g>
          <path d="M150 44 L150 112" stroke="#8C6A33" stroke-width="1.4"/></svg>
      </div>
    </article>

    <article class="vmv-card" id="vmv-vision" aria-label="{{ __('site.about.vision_title') }}">
      <span class="vmv-badge"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg></span>
      <h3>{{ __('site.about.vision_title') }}</h3><span class="vmv-rule" aria-hidden="true"></span>
      <p class="lead">{{ $p->label('vision') }}</p>
      <div class="vmv-art art-vision" aria-hidden="true">
        <svg viewBox="30 -130 260 380" preserveAspectRatio="xMidYMax slice"><defs>
          <radialGradient id="hz" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#FFD98F" stop-opacity=".55"/><stop offset="1" stop-color="#FFD98F" stop-opacity="0"/></radialGradient>
          <linearGradient id="bm" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#FFE7B0"/><stop offset="1" stop-color="#FFE7B0" stop-opacity="0"/></linearGradient>
          <linearGradient id="rd" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#E0BC72"/><stop offset="1" stop-color="#FFF6DF"/></linearGradient>
          <linearGradient id="rdF" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#E9CF96" stop-opacity=".55"/><stop offset="1" stop-color="#FFF6DF" stop-opacity=".05"/></linearGradient>
          <filter id="bl"><feGaussianBlur stdDeviation="4"/></filter></defs>
          <g class="v-grid" stroke="#E9CF96" fill="none">
            <path d="M160 128 L-220 250M160 128 L-60 250M160 128 L60 250M160 128 L260 250M160 128 L380 250M160 128 L540 250" stroke-opacity=".16"/>
            <path d="M0 140H320M0 158H320M0 184H320M0 222H320" stroke-opacity=".1"/></g>
          <ellipse cx="160" cy="128" rx="170" ry="22" fill="url(#hz)"/>
          <g class="v-rays" stroke="#FFE7B0" stroke-width="1" stroke-linecap="round"><line x1="160" y1="110" x2="160" y2="40" transform="rotate(0.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(22.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(45.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(67.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(90.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(112.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(135.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(157.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(180.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(202.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(225.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(247.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(270.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(292.5 160 110)"/><line x1="160" y1="110" x2="160" y2="40" transform="rotate(315.0 160 110)"/><line x1="160" y1="110" x2="160" y2="66" transform="rotate(337.5 160 110)"/></g>
          <polygon points="88,252 157,116 163,116 232,252" fill="url(#rdF)" opacity=".55"/>
          <path d="M88 252 L157 116 M232 252 L163 116" stroke="#E9CF96" stroke-width="2" fill="none" opacity=".9"/>
          <path d="M88 252 L157 116 M232 252 L163 116" stroke="#E9CF96" stroke-width="8" fill="none" opacity=".25" filter="url(#bl)"/>
          <g class="v-lanes" fill="none" stroke-linecap="round">
            <path class="v-flow" pathLength="100" d="M160 252 L160 118" stroke="#fff" stroke-width="3"/>
            <path class="v-flow f2" pathLength="100" d="M124 252 L158.5 118" stroke="#FFE7B0" stroke-width="2"/>
            <path class="v-flow f3" pathLength="100" d="M196 252 L161.5 118" stroke="#FFE7B0" stroke-width="2"/>
          </g>
          <circle class="v-ring" cx="160" cy="110" r="10" fill="none" stroke="#FFE7B0"/>
          <circle class="v-ring r2" cx="160" cy="110" r="10" fill="none" stroke="#FFE7B0"/>
          <circle class="v-ring r3" cx="160" cy="110" r="10" fill="none" stroke="#FFE7B0"/>
          <circle cx="160" cy="110" r="14" fill="#FFE7B0" opacity=".35" filter="url(#bl)"/>
          <path class="v-star" d="M160 98 L163 107 L172 110 L163 113 L160 122 L157 113 L148 110 L157 107Z" fill="#FFF8E6"/>
          <g class="v-tw" fill="#FFF1D2"><circle cx="40" cy="30" r="1.2"/><circle cx="270" cy="50" r="1.5"/><circle cx="90" cy="80" r="1"/><circle cx="230" cy="18" r="1"/><circle cx="295" cy="96" r="1.1"/><circle cx="70" cy="-90" r="1.3"/><circle cx="250" cy="-60" r="1"/><circle cx="120" cy="-30" r="1.1"/><circle cx="210" cy="-110" r="1.4"/><circle cx="60" cy="-20" r="1"/><circle cx="20" cy="100" r="1"/></g>
        </svg>
      </div>
    </article>

    <article class="vmv-card" id="vmv-values" aria-label="{{ __('site.about.values_title') }}">
      <span class="vmv-badge"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12l4 6-10 12L2 9z"/><path d="M2 9h20M12 21 8 9l4-6 4 6z"/></svg></span>
      <h3>{{ __('site.about.values_title') }}</h3><span class="vmv-rule" aria-hidden="true"></span>
      <ul class="v-list"><span class="v-dot" aria-hidden="true"></span><li style="--i:0"><div class="v-tx">{{ $p->label('value1') }}<br><span class="v-tag">{{ $p->label('value1_tag') }}</span></div><span class="v-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg></span></li><li style="--i:1"><div class="v-tx">{{ $p->label('value2') }}<br><span class="v-tag">{{ $p->label('value2_tag') }}</span></div><span class="v-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-8-5.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.5-8 11-8 11z"/></svg></span></li><li style="--i:2"><div class="v-tx">{{ $p->label('value3') }}<br><span class="v-tag">{{ $p->label('value3_tag') }}</span></div><span class="v-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0 0 12 2z"/></svg></span></li><li style="--i:3"><div class="v-tx">{{ $p->label('value4') }}<br><span class="v-tag">{{ $p->label('value4_tag') }}</span></div><span class="v-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0zM7 6H4a3 3 0 0 0 3 4M17 6h3a3 3 0 0 1-3 4"/></svg></span></li></ul>
      <div class="v-foot" aria-hidden="true"></div>
    </article>
  </div>
  <svg class="vmv-ribbon" viewBox="0 0 400 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46 C110 6 250 70 400 18" fill="none" stroke="#E9CF96" stroke-opacity=".5"/><path d="M0 54 C120 16 250 78 400 28" fill="none" stroke="#E9CF96" stroke-opacity=".25" stroke-dasharray="2 5"/></svg>
</section>

@include('site.sections.about.parts', ['page' => $p, 'em' => $em])
  <x-slot:afterFooter>@include('site.sections.about.after-footer')</x-slot:afterFooter>
  <x-slot:afterPage>@include('site.sections.about.templates')</x-slot:afterPage>
</x-site.layout>
