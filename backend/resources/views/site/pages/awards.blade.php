{{-- Design: awards.html (hero words by js awards-hero-words, the cards from the awards with a card style). --}}
@php
    [$line1, $line2] = array_pad(explode('|', (string) $page->hero_title, 2), 2, '');
    $portrait = $page->getFirstMedia('hero');
    $fields = $awards->map(fn ($a) => $a->label('field_label'))->filter();
@endphp
<x-site.layout :page="$page" styles="awards" body-class="flush" class="aws-page" :crumb="['title' => $page->title]">
  <section class="aws-hero aws-words" data-aws-words aria-labelledby="awards-title">
    <div class="aws-hero-inner">
      <div class="aws-hero-copy">
        <h1 id="awards-title">{{ $line1 }}@if ($line2)<br><span class="ap-shine" data-t="{{ $line2 }}">{{ $line2 }}</span>@endif</h1>
        <span class="aws-hero-rule" aria-hidden="true"></span>
        @if ($page->hero_lead)<p class="aws-hero-lead">{{ $page->hero_lead }}</p>@endif
        @if ($fields->isNotEmpty())<ul class="aws-hero-fields" aria-label="{{ __('site.awards.fields') }}">@foreach ($fields as $field)<li>{{ $field }}</li>@endforeach</ul>@endif
        @if ($page->hero_eyebrow)<span class="aws-hero-tribute">{{ $page->hero_eyebrow }}</span>@endif
      </div>
      @if ($portrait)
        <div class="aws-hero-portrait"><img src="{{ \App\Support\Media\Src::of($portrait) }}" alt="{{ $portrait->getCustomProperty('alt', '') }}" width="967" height="1626" fetchpriority="high" decoding="async"></div>
      @endif
    </div>
  </section>
  <div class="wrap aws-list">
    @foreach ($awards as $award)
      <a class="aws-card is-{{ $award->card_style }} has-podium" href="{{ $award->url() }}">
        <span class="aws-art aws-podium" aria-hidden="true"><span class="aws-halo"></span>
          @if ($art = $award->image('card_art'))<span class="aws-trophy"><img src="{{ \App\Support\Media\Src::of($art) }}" alt="" decoding="async"></span>
          @else<span class="aws-trophy aws-emblem"><span class="aws-gold"></span></span>@endif
          <span class="aws-floor"></span></span>
        <span class="aws-txt"><small>{{ $award->card_meta }}</small><b>{{ $award->name }}</b>
          @if ($award->card_text)<em>{{ $award->card_text }}</em>@endif<span class="aws-go">{{ __('site.awards.enter') }} ←</span></span>
      </a>
    @endforeach
  </div>
</x-site.layout>
