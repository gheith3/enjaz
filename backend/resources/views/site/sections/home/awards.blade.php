{{-- Design index.html .home-awards: the awards that have a card (two squares: logo, name, latest winners) and a banner
     picked in the section's items (image = background, title with | for a new line, chips in "meta" separated by |). --}}
@php
    $awards = \App\Models\Award::query()->listed()->whereNotNull('card_style')->with(['media', 'editions.winners.media'])->take(2)->get();
    $banner = $section->items()->with(['media', 'itemable.media'])->first();
    $split = function ($award) {
        $name = (string) $award->name; $sub = (string) $award->subtitle;

        return $sub && str_ends_with($name, $sub) ? [trim(mb_substr($name, 0, mb_strlen($name) - mb_strlen($sub))), $sub] : ['', $name];
    };
@endphp
@if ($awards->isNotEmpty() || $banner)
<section class="section wrap home-awards" aria-labelledby="awards-title">
  <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="awards-title">{{ $section->text('title', __('site.homepage.awards')) }}</h2></div>
  @if ($lead = $section->text('lead'))<p class="ha-lead">{{ $lead }}</p>@endif
  <div class="ha-grid">
    <div class="ha-pair">
      @foreach ($awards as $a)
        @php
            [$kick, $name] = $split($a);
            $faces = $a->editions->sortByDesc('sort_order')->flatMap->winners->map(fn ($w) => $w->imageUrl('photo', 'card'))->filter()->take(5);
        @endphp
        <a class="ha-sq" href="{{ $a->url() }}">
          <span class="ha-logo">@if ($logo = $a->imageUrl('logo', 'card'))<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
          @if ($kick)<small class="ha-kick">{{ $kick }}</small>@endif
          <b class="ha-name">{{ $name }}</b>
          @if ($a->motto)<span class="ha-meta">«{{ $a->motto }}»</span>@endif
          <span class="ha-foot"><span class="ha-stack">@foreach ($faces as $f)<img src="{{ $f }}" alt="" loading="lazy">@endforeach</span><span class="ha-go" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg></span></span>
        </a>
      @endforeach
    </div>
    @if ($banner)
      @php $award = $banner->itemable; $lines = explode('|', $banner->displayTitle()); @endphp
      <a class="ha-bh" href="{{ $banner->href() ?? '#' }}">
        @if ($bg = $banner->imageUrl('image'))<img class="ha-bh-bg" src="{{ $bg }}" alt="" loading="lazy">@endif
        <span class="ha-bh-in">@if ($mark = $award?->imageUrl('mark', 'card'))<img class="ha-bh-mark" src="{{ $mark }}" alt="" loading="lazy">@endif
          @if ($banner->label)<small>{{ $banner->label }}</small>@endif<b>{!! collect($lines)->map(fn ($l) => e(trim($l)))->implode('<br>') !!}</b>
          @if ($banner->text)<em>«{{ $banner->text }}»</em>@endif
          @if ($chips = array_filter(array_map('trim', explode('|', (string) \App\Support\Localized::get($banner->settings['meta'] ?? null)))))
            <span class="ha-bh-meta">@foreach ($chips as $c)<span>{{ $c }}</span>@endforeach</span>
          @endif
          <span class="ha-bh-cta">{{ $banner->link_label ?: __('site.homepage.discover_award') }} <span aria-hidden="true">←</span></span></span>
      </a>
    @endif
  </div>
</section>
@endif
