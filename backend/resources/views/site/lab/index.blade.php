{{-- Design: science.html (js/lab-showcase.js, lab-list.js, science-page.js). Everything is rendered here; the
     scripts only rotate the showcase, switch the phone tabs and count the numbers up. --}}
@php
    $p = $page;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n ?? '');
    $l = fn (string $key) => $p->label($key, __('site.lab.'.$key));
    $nInv = (int) ($p->label('stat_inventions') ?: $inventions->count());
    $nAll = (int) ($p->label('stat_science') ?: $inventions->count() + $publications->count());
    $portraits = $p->getMedia('hero');
    $cutout = (\App\Support\Media\Src::of($p->getFirstMedia('cutout')) ?? '');
    $count = fn ($n) => \App\Support\ArabicDate::digits($n);
@endphp
<x-site.layout :title="$p->title" :description="$p->meta_description" styles="lab"
    :crumb="['title' => $p->label('crumb', __('site.lab.crumb')), 'url' => null, 'sub' => null]">
<div class="lab-page" data-lab-page>
<section class="lab-hero" aria-labelledby="lab-title">
  <div class="lab-sci" aria-hidden="true">
    <svg class="sci-orbits" viewBox="0 0 600 600"><ellipse class="o1" cx="300" cy="300" rx="280" ry="96"/><ellipse class="o2" cx="300" cy="300" rx="280" ry="96"/><ellipse class="o3" cx="300" cy="300" rx="280" ry="96"/><circle class="e1" r="5"/><circle class="core" cx="300" cy="300" r="7"/></svg>
    <svg class="sci-mol" viewBox="0 0 320 240"><g class="mol"><path d="M60 80 90 63 120 80 120 115 90 132 60 115Z M120 80 150 63 180 80 180 115 150 132 120 115 M150 63 150 30 M180 115 210 132 240 115 M240 115 240 80 270 63 M90 132 90 165 60 182"/><circle cx="60" cy="80" r="4"/><circle cx="120" cy="80" r="4"/><circle cx="180" cy="115" r="4"/><circle cx="150" cy="30" r="5"/><circle cx="240" cy="115" r="4"/><circle cx="270" cy="63" r="5"/><circle cx="60" cy="182" r="5"/><circle cx="90" cy="132" r="4"/></g></svg>
    <span class="sci-glow"></span>
    <i class="bub" style="--x:8%;--d:0s"></i><i class="bub" style="--x:14%;--d:1.4s"></i><i class="bub" style="--x:20%;--d:2.6s"></i><i class="bub" style="--x:11%;--d:3.6s"></i><i class="bub" style="--x:17%;--d:4.8s"></i>
    @if ($cutout)<img class="sci-cut" src="{{ $cutout }}" alt="" decoding="async">@endif
  </div>
  <svg class="lab-net" viewBox="0 0 200 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><line x1="90" y1="46" x2="109" y2="20" style="animation-delay:0.00s"/><line x1="90" y1="46" x2="116" y2="23" style="animation-delay:0.12s"/><line x1="90" y1="46" x2="65" y2="23" style="animation-delay:0.24s"/><line x1="109" y1="20" x2="116" y2="23" style="animation-delay:0.36s"/><line x1="109" y1="20" x2="65" y2="23" style="animation-delay:0.48s"/><line x1="26" y1="145" x2="32" y2="101" style="animation-delay:0.60s"/><line x1="26" y1="145" x2="31" y2="149" style="animation-delay:0.72s"/><line x1="32" y1="101" x2="25" y2="69" style="animation-delay:0.84s"/><line x1="32" y1="101" x2="31" y2="149" style="animation-delay:0.96s"/><line x1="157" y1="22" x2="116" y2="23" style="animation-delay:1.08s"/><line x1="157" y1="22" x2="152" y2="39" style="animation-delay:1.20s"/><line x1="137" y1="62" x2="119" y2="115" style="animation-delay:1.32s"/><line x1="137" y1="62" x2="116" y2="23" style="animation-delay:1.44s"/><line x1="137" y1="62" x2="152" y2="39" style="animation-delay:1.56s"/><line x1="137" y1="62" x2="155" y2="109" style="animation-delay:1.68s"/><line x1="17" y1="30" x2="25" y2="69" style="animation-delay:1.80s"/><line x1="17" y1="30" x2="65" y2="23" style="animation-delay:1.92s"/><line x1="119" y1="115" x2="155" y2="109" style="animation-delay:2.04s"/><circle cx="90" cy="46" r="2.8"/><circle cx="109" cy="20" r="2"/><circle cx="26" cy="145" r="2"/><circle cx="32" cy="101" r="2.8"/><circle cx="157" cy="22" r="2"/><circle cx="137" cy="62" r="2"/><circle cx="17" cy="30" r="2.8"/><circle cx="119" cy="115" r="2"/><circle cx="25" cy="69" r="2"/><circle cx="31" cy="149" r="2.8"/><circle cx="116" cy="23" r="2"/><circle cx="152" cy="39" r="2"/><circle cx="65" cy="23" r="2.8"/><circle cx="155" cy="109" r="2"/></svg>
  <div class="wrap lab-hero-layout"><div class="lab-hero-copy">
    <span class="lab-kicker">{{ $p->hero_eyebrow ?: __('site.lab.kicker') }}</span>
    <h1 id="lab-title">{{ $p->hero_title ?: __('site.lab.title') }}</h1>
    <p class="lab-hero-description"><span class="lab-hero-subtitle">{{ $l('subtitle') }}</span>@if ($p->hero_lead)<span class="lab-hero-summary"> {{ $p->hero_lead }}</span>@endif</p>
    <div class="lab-stats">
      <div class="lab-stat"><b data-count="{{ $nInv }}">{{ $count($nInv) }}</b><small>{{ $p->label('stat_inventions_label', __('site.lab.stat_inventions')) }}</small></div>
      <div class="lab-stat"><b data-count="{{ $nAll }}">{{ $count($nAll) }}</b><small>{{ $p->label('stat_science_label', __('site.lab.stat_science')) }}</small></div>
    </div>
    @if ($note = $p->label('stats_note'))<p class="lab-src">{{ $note }}</p>@endif
  </div>
  @if ($portraits->isNotEmpty())
    <div class="lab-hero-portraits" aria-hidden="true">@foreach ($portraits->take(3) as $m)<img src="{{ \App\Support\Media\Src::of($m) }}" alt="" decoding="async">@endforeach</div>
  @endif
  </div>
</section>
<div class="wrap">
  <div class="lab-dk" aria-label="{{ __('site.lab.sections') }}">
    <section class="lx-show" aria-labelledby="lx-inv-h">
      <header class="lx-head"><span class="lx-tag">{{ $l('showcase_tag') }}</span><h2 id="lx-inv-h">{{ $p->label('showcase_title', __('site.lab.inventions')) }}</h2></header>
      <div data-lx-show>@if ($inventions->isNotEmpty())@php $n = $inventions->count(); @endphp
        <div class="lx-stage">
          <div class="lx-pics">@foreach ($inventions as $i => $x)<img src="{{ $x->imageUrl('cover') }}" alt="{{ \App\Models\LabItem::alt($x->image('cover')) }}" class="{{ $i ? '' : 'on' }}" loading="{{ $i ? 'lazy' : 'eager' }}">@endforeach<span class="lx-scan" aria-hidden="true"></span></div>
          <div class="lx-info">
            @foreach ($inventions as $i => $x)
              <article class="lx-item{{ $i ? '' : ' on' }}" aria-hidden="{{ $i ? 'true' : 'false' }}">
                <div class="lx-count"><b>{{ $count($i + 1) }}</b><span>/ {{ $count($n) }}</span></div>
                <div class="lx-meta"><span class="lx-medal {{ $x->medal }}">{{ $x->medalLabel() }}</span><span>{{ $x->event_label }}</span><span>{{ $x->place }}</span></div>
                <h3>{{ $x->cardTitle() }}</h3>
                <div class="lx-who"><b>{{ $x->who }}</b><span>{{ $x->orgName() }}@if ($x->field) · {{ $x->field }}@endif</span></div>
                <a class="lx-go" href="{{ $x->url() }}" tabindex="{{ $i ? '-1' : '0' }}">{{ __('site.lab.read_story') }} <span aria-hidden="true">←</span></a>
              </article>
            @endforeach
          </div>
        </div>
        <div class="lx-slides" role="tablist" aria-label="{{ __('site.lab.slides') }}">
          @foreach ($inventions as $i => $x)
            <button type="button" role="tab" class="lx-slide{{ $i ? '' : ' on' }}" aria-selected="{{ $i ? 'false' : 'true' }}"><span class="lx-sn">{{ $count($i + 1) }}</span><img src="{{ $x->imageUrl('cover', 'card') }}" alt="" loading="lazy"><span class="lx-st">{{ $x->cardTitle() }}<small>{{ $x->who }}</small></span><i class="lx-bar"></i></button>
          @endforeach
        </div>
        <a class="lx-big-all" href="{{ route('lab.list', 'inventions') }}"><span>{{ __('site.lab.all_inventions') }}</span><small>{{ __('site.lab.n_inventions', ['count' => $count($n)]) }}</small><i aria-hidden="true">←</i></a>
      @endif</div>
    </section>
    <div class="lx-sep" aria-hidden="true"><span></span><svg viewBox="0 0 60 24"><path d="M30 2 36 12 30 22 24 12Z"/><circle cx="12" cy="12" r="2.5"/><circle cx="48" cy="12" r="2.5"/></svg><span></span></div>
    <section class="lx-table" aria-labelledby="lx-pub-h">
      <header class="lx-head"><span class="lx-tag">{{ $l('table_tag') }}</span><h2 id="lx-pub-h">{{ $p->label('table_title', __('site.lab.publications')) }}</h2></header>
      <div data-lx-table>@if ($publications->isNotEmpty())
        <div class="lx-grid">
          @foreach ($publications as $i => $x)
            <a class="lx-el" data-f="{{ $x->field }}" href="{{ $x->url() }}">
              <span class="lx-pic">@if ($img = $x->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif @if ($x->cover_is_illustrative)<em>{{ __('site.lab.illustrative') }}</em>@endif</span>
              <span class="lx-no">{{ $count($i + 1) }}</span><span class="lx-yr">{{ $count($x->year()) }}</span>
              <span class="lx-num">{{ $x->finding_value }}</span>
              <b>{{ $x->title }}</b>
              <span class="lx-by">{{ $x->who }}@if ($x->journal)<small>{{ $x->journal }}</small>@endif</span>
            </a>
          @endforeach
        </div>
        <a class="lx-big-all" href="{{ route('lab.list', 'publications') }}"><span>{{ __('site.lab.all_publications') }}</span><small>{{ __('site.lab.n_publications', ['count' => $count($publications->count())]) }}</small><i aria-hidden="true">←</i></a>
      @endif</div>
    </section>
  </div>
  <div class="lab-mobile-sections">
  <div class="lab-switch" role="tablist" aria-label="{{ __('site.lab.tabs') }}" data-tab="inv">
    <span class="pill" aria-hidden="true"></span>
    <button type="button" role="tab" id="tab-inv" aria-controls="panel-inv" aria-selected="true" data-tab="inv">{{ __('site.lab.inventions') }} <small>{{ $count($inventions->count()) }}</small></button>
    <button type="button" role="tab" id="tab-pubs" aria-controls="panel-pubs" aria-selected="false" tabindex="-1" data-tab="pubs">{{ __('site.lab.publications') }} <small>{{ $count($publications->count()) }}</small></button>
  </div>
  @foreach (['inv' => [$inventions, 'inventions'], 'pubs' => [$publications, 'publications']] as $tab => [$list, $segment])
    <section class="lab-panel" id="panel-{{ $tab }}" role="tabpanel" aria-labelledby="tab-{{ $tab }}" @if ($tab === 'pubs') hidden @endif><div data-lab-list="{{ $tab }}">
      <div class="lb-grid">@foreach ($list->take(6) as $x)<x-site.lab.card :item="$x" />@endforeach</div>
      <a class="lb-more" href="{{ route('lab.list', $segment) }}">{{ __('site.lab.all_'.$segment) }} <small>{{ $count($list->count()) }}</small> <span aria-hidden="true">←</span></a>
    </div></section>
  @endforeach
  </div>
  <div class="lab-cta">
    <b>{{ $l('cta_title') }}</b>
    <small>{{ $l('cta_text') }}</small>
    <a class="btn" href="{{ $p->hero_cta_url ?: url('/contact') }}">{{ $p->hero_cta_label ?: __('site.lab.cta_button') }}</a>
  </div>
</div>
</div>
</x-site.layout>
