{{-- Design: articles.html (js/articles-page.js, articles-hero-wall.js). The list (tabs + search) is Livewire MagazineIndex;
     the hero search sends its text to it. --}}
@php
    $p = $page;
    $ornament = Vite::asset('resources/site/assets/svg/ornament.svg');
    $l = fn (string $key) => $p->label($key, __('site.magazine.'.$key));
    [$mast1, $mast2] = array_pad(explode('|', $l('mast_title'), 2), 2, '');
    $count = fn (int $n) => \App\Support\ArabicCount::of($n, 'articles');
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5m6-6-6 6 6 6"/></svg>';
    $cta = $p->label('cta_url') ?: url('/contact');
    $wallJson = $wall->map(fn ($a) => ['img' => $a->imageUrl('cover', 'card'), 't' => $a->title, 'href' => $a->url()])->values()->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp
<x-site.layout :title="$p->title" :description="$p->meta_description" styles="magazine"
    :crumb="['title' => $p->title, 'url' => null, 'sub' => null]">
<section class="page-hero mag-hero" aria-labelledby="mag-hero-title">
  <div class="mag-hero-copy"><span class="mag-hero-kicker">{{ $p->hero_eyebrow ?: __('site.magazine.kicker') }}</span><h1 id="mag-hero-title">{{ $p->hero_title ?: __('site.magazine.title') }}</h1>
    @if ($p->hero_lead)<p class="mag-hero-intro">{{ $p->hero_lead }}</p>@endif
    <form class="search" role="search" action="{{ route('magazine.index') }}#mag-all" x-data @submit.prevent="$dispatch('mag-search', { q: $refs.q.value }); document.getElementById('mag-all')?.scrollIntoView({ block: 'start' })">
      <label class="sr-only" for="q-art">{{ __('site.magazine.search') }}</label>
      <input id="q-art" type="search" name="q" placeholder="{{ __('site.magazine.search_placeholder') }}" data-art-search x-ref="q" value="{{ request('q') }}"
        @input.debounce.250ms="$dispatch('mag-search', { q: $el.value }); if ($el.value.trim()) { const l = document.getElementById('mag-all'); if (l && l.getBoundingClientRect().top > innerHeight) l.scrollIntoView({ block: 'start' }); }">
      <button type="submit" aria-label="{{ __('site.magazine.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button></form>
    <a class="mag-hero-link" href="#mag-all">{{ $p->hero_cta_label ?: __('site.magazine.browse') }} <span aria-hidden="true">←</span></a></div>
  <div class="mag-hero-art" aria-hidden="true" data-mag-wall="{{ $wallJson }}">
    <div class="mag-hero-image"></div><span class="mag-hero-quote">“</span>
    <div class="mag-hero-note"><span>{{ $l('note_kicker') }}</span><b>{!! collect(explode('|', $l('note_text')))->map(fn ($s) => e($s))->implode('<br>') !!}</b><i></i></div></div>
</section>
<div class="mag-page">
  @if ($issue)
    <header class="wrap mag-mast">
      <div class="mm-line"><span>{{ $issue->label() }}</span><i></i><img src="{{ $ornament }}" alt=""><i></i><span>{{ $issue->dateLabel() }}</span></div>
      <p class="mm-title">{{ $mast1 }} @if ($mast2)<span>{{ $mast2 }}</span>@endif</p>
      @if ($issue->tagline)<p class="mm-tag">{{ $issue->tagline }}</p>@endif
    </header>
  @endif

  @if ($lead)
    <article class="wrap mag-lead" data-cat="{{ $lead->category?->slug }}">
      <a class="cover-link" href="{{ $lead->url() }}" aria-label="{{ $lead->title }}"></a>
      @if ($cover = $lead->image('cover'))<img class="gold-ph" src="{{ $lead->imageUrl('cover') }}" alt="{{ \App\Models\Article::alt($cover) }}">@endif
      <div class="ml-b">
        <p class="kick">{{ __('site.magazine.issue_lead') }}@if ($lead->category) · {{ $lead->category->name }}@endif</p>
        <h2>{{ $lead->title }}</h2>
        @if ($lead->dek)<p class="ex">{{ $lead->dek }}</p>@endif
        @if ($w = $lead->writer)
          <div class="byline"><span class="av" aria-hidden="true">@if ($ph = $w->photoUrl())<img src="{{ $ph }}" alt="">@endif</span><span><b>{{ $w->name }}</b>@if ($w->role)<small>{{ $w->role }}</small>@endif</span>
            <span class="ring" style="--p:{{ min(1, $lead->read_minutes / 10) }}" title="{{ __('site.magazine.read_time', ['n' => \App\Support\ArabicDate::digits($lead->read_minutes)]) }}"><b>{{ \App\Support\ArabicDate::digits($lead->read_minutes) }}</b><small>{{ __('site.magazine.minutes_unit') }}</small></span></div>
        @endif
      </div>
    </article>
  @endif

  @if ($issue?->quote)
    <section class="mag-quote" aria-label="{{ __('site.magazine.quote') }}">
      <div class="wrap"><span class="mq-mark" aria-hidden="true">”</span>
        <blockquote>{{ $issue->quote }}</blockquote>
        @if ($issue->quote_cite)<cite>— {{ $issue->quote_cite }}</cite>@endif
      </div>
    </section>
  @endif

  <livewire:site.magazine-index :except-id="$lead?->id" />

  @if ($mostRead->isNotEmpty())
    <section class="wrap mag-sec" aria-labelledby="top-h">
      <h2 class="mag-h" id="top-h"><img src="{{ $ornament }}" alt="">{{ __('site.magazine.most_read') }}</h2>
      <ol class="mag-top">
        @foreach ($mostRead as $i => $a)
          <li><span class="n" aria-hidden="true">{{ \App\Support\ArabicDate::digits($i + 1) }}</span><div><h3><a href="{{ $a->url() }}">{{ $a->title }}</a></h3><small>@if ($a->writer){{ $a->writer->name }} · @endif{{ $a->minutesLabel() }}</small></div></li>
        @endforeach
      </ol>
    </section>
  @endif

  @if ($writers->isNotEmpty())
    <section class="wrap mag-sec" aria-labelledby="w-h">
      <div class="mag-h-row"><h2 class="mag-h" id="w-h"><img src="{{ $ornament }}" alt="">{{ __('site.magazine.writers') }}</h2><a class="mag-more mag-more-heading" href="{{ route('magazine.writers') }}"><span class="mm-arrow" aria-hidden="true">{!! $arrow !!}</span><small>{{ __('site.magazine.more_writers') }}</small></a></div>
      <div class="mag-writers-layout">
        <div class="mag-writers">
          @foreach ($writers->take(8) as $w)
            <a class="writer" href="{{ $w->url() }}"><span class="av" aria-hidden="true">@if ($ph = $w->photoUrl())<img src="{{ $ph }}" alt="">@endif</span><b>{{ $w->name }}</b><small>{{ $count($w->articles->count()) }}</small></a>
          @endforeach
        </div>
        <a class="mag-more mag-more-beside" href="{{ route('magazine.writers') }}"><span class="mm-arrow" aria-hidden="true">{!! $arrow !!}</span><small>{{ __('site.magazine.more_writers') }}</small></a>
      </div>
    </section>
  @endif

  <div class="wrap">
    <a class="mag-cta" href="{{ $cta }}">
      <span class="mc-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20z"/><path d="M14 7l3 3"/></svg></span>
      <span><b>{{ $l('cta_title') }}</b><small>{{ $l('cta_text') }}</small></span>
      <span class="go" aria-hidden="true">←</span>
    </a>
  </div>
</div>
</x-site.layout>
