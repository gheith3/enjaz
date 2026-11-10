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

  <section class="story-hero" aria-labelledby="achievement-title">
    @if ($coverUrl)
      <img class="story-photo" src="{{ $coverUrl }}" alt="{{ \App\Models\Achievement::alt($cover, $achievement->title) }}" fetchpriority="high" width="800" height="400" style="object-position:{{ \App\Models\Achievement::focal($cover) }}">
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
      <a class="story-author" href="{{ route('achievers.show', $achiever) }}" data-avatar="{{ $achiever->imageUrl('cutout') ?? $achiever->imageUrl('portrait', 'card') }}">
        <strong data-crumb-sub>{{ $achiever->name }}</strong>
        <span class="story-profile-link"><img src="{{ Vite::asset('resources/site/assets/svg/icon-omani-'.($achiever->isFemale() ? 'woman' : 'man').'-teal.svg') }}" alt="" width="22" height="22"><span>{{ __('site.achievements.profile') }}</span><svg viewBox="0 0 24 24" aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg></span>
      </a>
    @endif

    @if ($path->count() > 1)
      <x-site.achievement.path :path="$path" :current="$achievement" />
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
            {!! str($paragraph)->sanitizeHtml() !!}
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
  </div>

  @if ($others->isNotEmpty())
    <section class="section gray section-pad">
      <div class="wrap">
        <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2>{{ __('site.achievements.others') }}</h2></div>
        <div class="rows">
          @foreach ($others as $other)
            <x-site.cards.achievement :achievement="$other" variant="row" />
          @endforeach
        </div>
        @if ($achiever)
          <a class="story-back" href="{{ route('achievers.show', $achiever) }}">{{ __('site.achievements.discover', ['name' => $achiever->name]) }} <span aria-hidden="true">←</span></a>
        @endif
      </div>
    </section>
  @endif

  <x-slot:scripts>
    <x-site.achievement.share :achievement="$achievement" :achiever="$achiever" :photo="$coverUrl" />
  </x-slot:scripts>
</x-site.layout>
