{{-- Design: writers.html. Writers are sorted by their newest article (server); the tabs filter the cards (js magazine.js). --}}
@php
    $p = $page;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $ornament = Vite::asset('resources/site/assets/svg/ornament.svg');
    [$t1, $t2] = array_pad(explode('|', $p->hero_title ?: __('site.magazine.writers_title'), 2), 2, '');
    $count = fn (int $n) => \App\Support\ArabicCount::of($n, 'articles');
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m6-6-6 6 6 6"/></svg>';
    $faces = $writers->filter(fn ($w) => $w->hasMedia('photo'));
    $used = $writers->pluck('article_category_id')->filter()->unique();
@endphp
<x-site.layout :title="$p->title" :description="$p->meta_description" styles="writers"
    :crumb="['title' => $p->title, 'url' => null, 'sub' => null]">
<div class="mag-page wr-page" data-writers>
  <section class="wr-hero">
    <div class="wrap">
      <div class="mm-line"><span>{{ $p->label('kicker', __('site.magazine.kicker')) }}</span><i></i><img src="{{ $ornament }}" alt=""><i></i>@if ($issue)<span>{{ $issue->label() }}</span>@endif</div>
      <h1>{{ $t1 }} @if ($t2)<span>{{ $t2 }}</span>@endif</h1>
      <p>{{ $p->hero_lead ?: __('site.magazine.writers_intro') }}</p>
      @if ($faces->isNotEmpty())
        <div class="wr-faces" aria-hidden="true">@foreach ($faces->take(6) as $w)<img src="{{ $w->photoUrl() }}" alt="">@endforeach @if ($faces->count() > 6)<span>+{{ $d($faces->count() - 6) }}</span>@endif</div>
      @endif
      <dl class="wr-stats"><div><dt>{{ __('site.magazine.stat_writers') }}</dt><dd>{{ $d($writers->count()) }}</dd></div><div><dt>{{ __('site.magazine.stat_articles') }}</dt><dd>{{ $d($articlesCount) }}</dd></div><div><dt>{{ __('site.magazine.stat_fields') }}</dt><dd>{{ $d($used->count()) }}</dd></div></dl>
    </div>
  </section>

  @if ($featured)
    <section class="wrap wr-feature" aria-labelledby="wf-h">
      <p class="kick">{{ __('site.magazine.featured') }}</p>
      <div class="wf-card">
        <figure class="wr-ph wf-photo">@if ($ph = $featured->imageUrl('photo'))<img src="{{ $ph }}" alt="{{ $featured->name }}">@endif</figure>
        <div class="wf-b">
          <h2 id="wf-h">{{ $featured->name }}</h2>
          <p class="wr-role">{{ collect([$featured->role, $count($featured->articles->count())])->filter()->implode(' · ') }}</p>
          @if ($featured->quote)<blockquote>«{{ $featured->quote }}»</blockquote>@endif
          @if ($latest = $featured->articles->first())<a class="wf-link" href="{{ $latest->url() }}">{{ __('site.magazine.latest_article') }} <span aria-hidden="true">←</span></a>@endif
        </div>
      </div>
    </section>
  @endif

  <section class="wrap mag-sec" aria-labelledby="wl-h">
    <h2 class="mag-h" id="wl-h"><img src="{{ $ornament }}" alt="">{{ __('site.magazine.all_writers') }}</h2>
    <div class="mag-tabs" role="group" aria-label="{{ __('site.magazine.writers_tabs') }}"><button type="button" class="chip active" aria-pressed="true" data-tab="*"><span>{{ __('site.magazine.all_tab') }}</span></button>@foreach ($categories as $c)<button type="button" class="chip" aria-pressed="false" data-tab="{{ $c->slug }}"><span>{{ $c->name }}</span></button>@endforeach</div>
    <div class="wr-grid">
      @foreach ($writers as $i => $w)
        <article @class(['wr-card', 'is-newest' => $i === 0]) id="{{ $w->slug }}" data-cat="{{ $w->category?->slug }}">
          <div class="wr-top">
            <figure class="wr-ph">@if ($ph = $w->photoUrl())<img src="{{ $ph }}" alt="{{ $w->name }}" loading="lazy">@endif</figure>
            <div class="wr-id"><h3>{{ $w->name }}</h3>@if ($w->role)<p class="wr-role">{{ $w->role }}</p>@endif<p class="wr-meta">@if ($w->category)<span class="wr-tag">{{ $w->category->name }}</span>@endif<span class="wr-count">{{ $count($w->articles->count()) }}</span></p></div>
          </div>
          @if ($w->articles->isNotEmpty())
            <ul class="wr-arts" aria-label="{{ __('site.magazine.articles_of', ['name' => $w->name]) }}">
              @foreach ($w->articles as $a)<li><a href="{{ $a->url() }}"><span class="wa-t">{{ $a->title }}</span><span class="wa-m">{{ __('site.magazine.minutes_short', ['n' => $d($a->read_minutes)]) }}</span>{!! $arrow !!}</a></li>@endforeach
            </ul>
          @endif
        </article>
      @endforeach
    </div>
  </section>

  <div class="wrap">
    <a class="mag-cta" href="{{ $p->label('cta_url') ?: url('/contact') }}">
      <span class="mc-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20z"/><path d="M14 7l3 3"/></svg></span>
      <span><b>{{ $p->label('cta_title', __('site.magazine.writers_cta_title')) }}</b><small>{{ $p->label('cta_text', __('site.magazine.cta_text')) }}</small></span>
      <span class="go" aria-hidden="true">←</span>
    </a>
  </div>
</div>
</x-site.layout>
