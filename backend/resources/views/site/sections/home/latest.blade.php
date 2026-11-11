{{-- Design index.html .home-latest: achievements marked «في أحدث الإنجازات», by place (one big, two cards, two rows). --}}
@php
    $items = \App\Models\Achievement::query()->published()->where('show_on_home', true)->with('media')
        ->orderByDesc('achieved_at')->orderBy('sort_order')->get()->groupBy(fn ($a) => $a->home_slot ?: 'row');
    $feature = $items->get('feature', collect())->first();
    $cards = $items->get('card', collect())->take(2);
    $rows = $items->get('row', collect())->take(2);
    $title = fn ($a) => $a->home_title ?: $a->title;
    $img = function ($a, $conv = 'large') {
        $m = $a->image('cover');

        return $m ? ['src' => $a->imageUrl('cover', $conv), 'alt' => \App\Models\Achievement::alt($m, $a->title), 'pos' => $m->getCustomProperty('focal')] : null;
    };
    $more = __('site.homepage.discover');
@endphp
@if ($feature || $cards->isNotEmpty() || $rows->isNotEmpty())
<section class="section wrap home-latest" aria-labelledby="home-latest-title">
  <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="home-latest-title">{{ $section->text('title', __('site.homepage.latest')) }}</h2></div>
  <div class="latest-layout">
    @if ($feature)
      @php $i = $img($feature); @endphp
      <a class="latest-feature" href="{{ $feature->url() }}" aria-label="{{ __('site.homepage.discover_about', ['title' => $title($feature)]) }}">
        @if ($i)<img src="{{ $i['src'] }}" alt="{{ $i['alt'] }}" loading="lazy" @if ($i['pos']) style="object-position:{{ $i['pos'] }}" @endif>@endif
        <span class="latest-feature-content"><strong>{{ $title($feature) }}</strong><span class="latest-action">{{ $more }} <span aria-hidden="true">←</span></span></span>
      </a>
    @endif
    @if ($cards->isNotEmpty())
      <div class="latest-pair">
        @foreach ($cards as $a)
          @php $i = $img($a, 'card'); @endphp
          <a class="latest-card" href="{{ $a->url() }}">@if ($i)<img src="{{ $i['src'] }}" alt="{{ $i['alt'] }}" loading="lazy" @if ($i['pos']) style="object-position:{{ $i['pos'] }}" @endif>@endif<span class="latest-card-content"><strong>{{ $title($a) }}</strong><span class="latest-action">{{ $more }} <span aria-hidden="true">←</span></span></span></a>
        @endforeach
      </div>
    @endif
    @if ($rows->isNotEmpty())
      <div class="latest-rows">
        @foreach ($rows as $k => $a)
          @php $i = $img($a, 'card'); @endphp
          <a class="latest-row" href="{{ $a->url() }}">@if ($i)@if ($k === 1)<span class="latest-row-photo">@endif<img src="{{ $i['src'] }}" alt="{{ $i['alt'] }}" loading="lazy" @if ($i['pos']) style="object-position:{{ $i['pos'] }}" @endif>@if ($k === 1)</span>@endif @endif<span class="latest-row-content"><strong>{{ $title($a) }}</strong><span class="latest-action">{{ $more }} <span aria-hidden="true">←</span></span></span></a>
        @endforeach
      </div>
    @endif
  </div>
  <div class="center" style="margin-top:20px"><a class="btn-more" href="{{ route('achievements.index') }}">{{ $section->text('more_label', __('site.homepage.more_achievements')) }}</a></div>
</section>
@endif
