{{-- Design: achievement*.html (one template for every achievement). --}}
@php $cover = $achievement->image('cover'); @endphp
@php $coverUrl = $achievement->imageUrl('cover') ?? $achiever?->imageUrl('portrait'); @endphp
<x-site.layout :title="$achievement->title.($achiever ? ' — '.$achiever->name : '')" :description="$achievement->excerpt" :image="$coverUrl" styles="achievement"
    body-class="flush achievement-detail" og-type="article" :noindex="! $achievement->isLive()"
    :crumb="['title' => $listPage->title, 'url' => route('achievements.index'), 'sub' => $achiever?->name]">
  <x-slot:head>
    @if ($achievement->achieved_at)<meta property="article:published_time" content="{{ $achievement->achieved_at->toDateString() }}">@endif
    <script type="application/ld+json">@php $jsonData = [
      '@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $achievement->title, 'description' => $achievement->excerpt,
      'image' => $coverUrl, 'datePublished' => $achievement->achieved_at?->toDateString(), 'inLanguage' => app()->getLocale(),
      'about' => $achiever ? ['@type' => 'Person', 'name' => $achiever->name, 'url' => route('achievers.show', $achiever)] : null,
    ]; @endphp
{!! json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
  </x-slot:head>

  <div class="read-progress" aria-hidden="true"><i data-read-progress></i></div>

  <div @class(['achievement-reading-layout', 'has-sidebar' => $path->isNotEmpty()])>
  <div class="achievement-reading-main">
  <section class="story-hero" aria-labelledby="achievement-title">
    @if ($coverUrl)
      <img class="story-photo" src="{{ $coverUrl }}" alt="{{ \App\Models\Achievement::alt($cover, $achievement->title) }}" fetchpriority="high" width="800" height="400" style="object-position:{{ \App\Models\Achievement::focal($cover) }}">
      {{-- Not loaded yet when the page opens: keep it hidden and fade it in once it arrives (achievement-reading.css). --}}
      <script>(function (i) { if (i.complete || matchMedia('(prefers-reduced-motion: reduce)').matches) return; i.classList.add('is-waiting'); var show = function () { i.classList.add('is-in'); }; i.addEventListener('load', show, { once: true }); i.addEventListener('error', show, { once: true }); setTimeout(show, 3000); })(document.currentScript.previousElementSibling);</script>
    @endif
    <div class="story-heading">
      <div class="story-meta">
        @if ($achievement->scope)<span class="story-category">{{ $achievement->scope->achievement_label ?: $achievement->scope->name }}</span>@endif
        <span class="story-readtime">⏱ {{ __('site.achievements.read_time', ['time' => \App\Support\ArabicCount::of($achievement->readMinutes(), 'minutes')]) }}</span>
      </div>
      <h1 id="achievement-title">{{ $achievement->title }}</h1>
    </div>
  </section>

  <div class="story-body">
    <x-site.achievement.facts :achievement="$achievement" />

    @if ($achiever)
      <a class="story-author" href="{{ route('achievers.show', $achiever) }}" data-avatar="{{ $achiever->headerAvatarUrl() }}">
        <strong data-crumb-sub>{{ $achiever->name }}</strong>
        <span class="story-profile-link"><img src="{{ Vite::asset('resources/site/assets/svg/icon-omani-'.($achiever->isFemale() ? 'woman' : 'man').'-teal.svg') }}" alt="" width="22" height="22"><span>{{ __('site.achievements.profile') }}</span><svg viewBox="0 0 24 24" aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg></span>
      </a>
    @endif


    @if ($highlights = $achievement->highlightItems())
      <aside class="highlights" aria-labelledby="hl-title">
        <h2 id="hl-title"><span aria-hidden="true">✦</span> {{ __('site.achievements.highlights') }}</h2>
        <ul>
          @foreach ($highlights as $item)
            <li>@if ($item['lead'])<b>{{ $item['lead'] }}</b>@endif {{ $item['text'] }}</li>
          @endforeach
        </ul>
      </aside>
    @endif

    @if (filled($achievement->body))
      <article class="story-article" aria-labelledby="story-title">
        <div class="story-section-heading"><h2 id="story-title">{{ __('site.achievements.story') }}</h2></div>
        <div class="article" data-article>
          @foreach ($achievement->bodyParagraphs() as $paragraph)
            {{-- A long single paragraph is shown as a few shorter ones, split at sentence ends (no word changes). --}}
            @foreach (\App\Support\Paragraphs::html($paragraph) as $part)
              {!! str($part)->sanitizeHtml() !!}
            @endforeach
            @if ($loop->iteration === max(1, $achievement->pull_quote_after) && filled($achievement->pull_quote))
              <x-site.pull-quote :text="$achievement->pull_quote" :by="$achievement->pull_quote_by" :role="$achievement->pull_quote_role" :note="$achievement->pull_quote_note" />
            @endif
          @endforeach
        </div>
      </article>
    @endif

    @if ($achievement->sources->isNotEmpty())
      <section class="sources" aria-labelledby="src-title">
        <h2 id="src-title">{{ __('site.achievements.sources') }}</h2>
        <ol>
          @foreach ($achievement->sources as $source)
            <li>@if ($source->url)<a href="{{ $source->url }}" target="_blank" rel="noopener">@else<span>@endif<b>{{ $source->title }}</b>@if ($source->subtitle)<span dir="auto">{{ $source->subtitle }}</span>@endif @if ($source->url)</a>@else</span>@endif</li>
          @endforeach
        </ol>
      </section>
    @endif

    @php
      $at = $path->search(fn ($item) => $item->is($achievement));
      $previous = $at === false ? null : $path->get($at - 1);
      $next = $at === false ? null : $path->get($at + 1);
    @endphp
    @if ($previous || $next)
      {{-- Design 10: the achiever's previous and next achievement, in time order. --}}
      <nav class="story-pager" aria-label="{{ __('site.achievements.path_nav') }}">
        @foreach (['prev' => $previous, 'next' => $next] as $rel => $item)
          @if ($item)
            <a class="{{ $rel }}" href="{{ $item->url() }}" rel="{{ $rel }}">
              <img src="{{ $item->imageUrl('cover', 'card') ?? $achiever?->imageUrl('portrait', 'card') ?? Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achievement::focal($item->image('cover')) }}">
              <small>{{ $rel === 'prev' ? '→ '.__('site.achievements.previous') : __('site.achievements.next') }}@if ($item->achieved_at) · {{ \App\Support\ArabicDate::digits($item->achieved_at->year) }}@endif{{ $rel === 'next' ? ' ←' : '' }}</small>
              <strong>{{ $item->short_title ?: $item->title }}</strong>
            </a>
          @else
            <span aria-hidden="true"></span>
          @endif
        @endforeach
      </nav>
    @endif
  </div>

  </div>
  @if ($path->isNotEmpty())
    @php
      $d = fn ($n) => \App\Support\ArabicDate::digits($n);
      $years = $path->map(fn ($item) => $item->achieved_at?->year)->filter();
      $groups = collect(['intl' => true, 'local' => false])
          ->map(fn (bool $international) => $path->filter(fn ($item) => (bool) $item->scope?->is_international === $international)->sortByDesc('achieved_at')->values())
          ->filter(fn ($group) => $group->isNotEmpty());
      $openGroup = $groups->search(fn ($group) => $group->contains(fn ($item) => $item->is($achievement))) ?: $groups->keys()->first();
      $tabbed = $groups->count() > 1;
      $recordPhoto = $achiever->imageUrl('cutout') ?? $achiever->imageUrl('portrait', 'card');
    @endphp
    {{-- Design 10: the achiever's record — international / local tabs, the current achievement marked «تقرأ الآن». --}}
    <aside class="achievement-reading-sidebar" aria-labelledby="ars-title">
      <div class="ars" data-ars>
        <a class="ars-person" href="{{ route('achievers.show', $achiever) }}">
          <span class="ars-avatar">@if ($recordPhoto)<img src="{{ $recordPhoto }}" alt="" loading="lazy">@endif</span>
          <span class="ars-who">
            <small>{{ __('site.achievements.record') }}</small><strong id="ars-title">{{ $achiever->name }}</strong>
            @if ($years->isNotEmpty())<span>{{ __('site.achievements.record_span', ['count' => \App\Support\ArabicCount::of($path->count(), 'achievements'), 'from' => $d($years->min()), 'to' => $d($years->max())]) }}</span>@endif
          </span>
          <span class="ars-go" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></span>
        </a>
        @if ($tabbed)
          <div class="ars-tabs" role="tablist" aria-label="{{ __('site.achievements.record_tabs') }}">
            @foreach ($groups as $key => $group)
              <button type="button" @class(['ars-tab', 'is-local' => $key === 'local']) role="tab" id="ars-tab-{{ $key }}" aria-controls="ars-{{ $key }}" aria-selected="{{ $key === $openGroup ? 'true' : 'false' }}" @if ($key !== $openGroup) tabindex="-1" @endif>{{ __('site.achievements.record_'.$key) }} <span>{{ $d($group->count()) }}</span></button>
            @endforeach
          </div>
        @endif
        @foreach ($groups as $key => $group)
          <ol class="ars-list" id="ars-{{ $key }}" @if ($tabbed) role="tabpanel" aria-labelledby="ars-tab-{{ $key }}" @endif @if ($key !== $openGroup) hidden @endif>
            @foreach ($group as $item)
              @php $current = $item->is($achievement); @endphp
              <li>
                <a class="ars-item" href="{{ $item->url() }}" @if ($current) aria-current="page" @endif>
                  <img src="{{ $item->imageUrl('cover', 'card') ?? $achiever->imageUrl('portrait', 'card') ?? Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="" loading="lazy" width="58" height="58" style="object-position:{{ \App\Models\Achievement::focal($item->image('cover')) }}">
                  <span><small>{{ $item->dateLabel() }}@if ($current) <b class="ars-now">{{ __('site.achievements.reading_now') }}</b>@endif</small><strong>{{ $item->short_title ?: $item->title }}</strong></span>
                </a>
              </li>
            @endforeach
          </ol>
        @endforeach
        <a class="ars-all" href="{{ route('achievers.show', $achiever) }}#achievements">{{ __('site.achievements.record_all', ['name' => $achiever->name]) }} <span aria-hidden="true">←</span></a>
      </div>
    </aside>
  @endif
  </div>

  <x-slot:scripts>
    <x-site.achievement.share :achievement="$achievement" :achiever="$achiever" :photo="$coverUrl" />
  </x-slot:scripts>
</x-site.layout>
