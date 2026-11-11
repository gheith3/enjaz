{{-- Design: inspiring.html (js/inspiring-strip.js panels, inspiring-hero-live.js wall captions, wall filter). People: achievers
     marked «ملهمون» (field, tagline, panel photo, portrait, cover). Then the page's sections (همم بلا حدود) and the nominate card. --}}
@php
    $p = $page;
    [$gold, $rest] = array_pad(explode('|', $p->hero_title ?: __('site.inspiring.title'), 2), 2, '');
    $field = fn ($a) => $a->inspiring_field ?: $a->field?->name;
    $fields = $people->map($field)->filter()->unique()->values();
    // Photo wall: 8 columns of 16 tiles, each column starting at another person and photo (as the design's columns).
    $photos = $people->map(fn ($a) => collect(['inspiring_panel', 'portrait', 'inspiring_cover'])->map(fn ($c) => $a->getFirstMediaUrl($c))->filter()->values()
        ->map(fn ($src) => ['src' => $src, 'name' => $a->name, 'field' => $field($a)]))->filter(fn ($l) => $l->isNotEmpty())->values();
@endphp
<x-site.layout :title="$p->title" :description="$p->meta_description ?: $p->hero_lead" styles="inspiring"
    :crumb="['title' => $p->label('crumb', $p->title), 'url' => null, 'sub' => null]">
<section class="page-hero ins-strip" aria-labelledby="is-title">
  <div class="is-head">
    @if ($p->hero_eyebrow)<span class="is-eyebrow">{{ $p->hero_eyebrow }}</span>@endif
    <h1 class="is-title" id="is-title"><span class="is-gold">{{ $gold }}</span> {{ $rest }}</h1>
    @if ($p->hero_lead)<p class="is-intro">{{ $p->hero_lead }}</p>@endif
    @if ($fields->isNotEmpty())<ul class="is-fields" aria-label="{{ __('site.inspiring.fields') }}">@foreach ($fields as $f)<li>{{ $f }}</li>@endforeach</ul>@endif
    <a class="is-discover" href="#wall-h">{{ $p->label('discover', __('site.inspiring.discover')) }} <span aria-hidden="true">←</span></a>
  </div>
  @if ($photos->isNotEmpty())
  <div class="is-photo-media" aria-hidden="true">
  <div class="is-photo-wall">
    @for ($c = 0; $c < 8; $c++)
      <div class="is-photo-column"><div class="is-photo-track">
        @for ($i = 0; $i < 16; $i++)
          @php $set = $photos[($i + $c) % $photos->count()]; $ph = $set[($i + intdiv($c, 2)) % $set->count()]; @endphp
          <span class="is-photo-tile" data-name="{{ $ph['name'] }}" data-field="{{ $ph['field'] }}"><img src="{{ $ph['src'] }}" alt="" decoding="async"></span>
        @endfor
      </div></div>
    @endfor
  </div>
  </div>
  @endif
  <div class="is-panels" role="group" aria-label="{{ __('site.inspiring.people') }}">
    @foreach ($people as $i => $a)
      @php $img = $a->image('inspiring_panel') ?? $a->image('portrait'); @endphp
      <button type="button" @class(['is-panel', 'is-on' => $i === 0]) aria-pressed="{{ $i === 0 ? 'true' : 'false' }}" aria-label="{{ $a->name }} — {{ $field($a) }}">
        @if ($img)<img src="{{ $img->getUrl() }}" alt="" width="540" height="604" style="object-position:{{ $img->getCustomProperty('focal') ?: '50% 25%' }}" @if ($i > 1) loading="lazy" @endif>@endif
        <span class="is-v" aria-hidden="true">{{ $a->name }}</span>
        <span class="is-cap"><span class="is-field">{{ $field($a) }}</span><b class="is-name">{{ $a->name }}</b>@if ($a->inspiring_tagline)<span class="is-line">{{ $a->inspiring_tagline }}</span>@endif</span>
        <i class="is-bar" aria-hidden="true"></i>
      </button>
    @endforeach
  </div>
</section>
<section class="wrap ins-sec" aria-labelledby="wall-h">
  <h2 class="ins-h" id="wall-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ $p->label('wall_title', __('site.inspiring.wall')) }}</h2>
  <div class="wall-filter" role="group" aria-label="{{ __('site.inspiring.filter') }}" data-wall-filter>
    <button type="button" class="chip active" aria-pressed="true" data-field="*"><span>{{ __('site.inspiring.all') }}</span></button>
    @foreach ($fields as $f)<button type="button" class="chip" aria-pressed="false" data-field="{{ $f }}"><span>{{ $f }}</span></button>@endforeach
  </div>
  <div class="wall">
    @foreach ($people as $i => $a)
      @php $img = $a->image('portrait') ?? $a->image('inspiring_cover'); @endphp
      <a @class(['tile', 'tall' => in_array($i % 4, [0, 3], true)]) href="{{ $a->url() }}">@if ($img)<img src="{{ $img->getUrl() }}" alt="{{ $a->name }}" loading="lazy" style="object-position:{{ $img->getCustomProperty('focal') ?: '50% 25%' }}">@endif<span class="tile-b"><span>{{ $field($a) }}</span><b>{{ $a->name }}</b></span></a>
    @endforeach
    {{-- The tall tiles leave two holes in a grid of four; like the design, fill them with covers of the same people. --}}
    @if ($people->count() % 4 === 0)
      @foreach ($people->values()->filter(fn ($a, $k) => $k % 2 === 1 && $a->hasMedia('inspiring_cover'))->take(2) as $a)
        <a class="tile" href="{{ $a->url() }}" tabindex="-1" aria-hidden="true"><img src="{{ $a->getFirstMediaUrl('inspiring_cover') }}" alt="" loading="lazy"><span class="tile-b"><span>{{ $field($a) }}</span><b>{{ $a->name }}</b></span></a>
      @endforeach
    @endif
  </div>
</section>
@include('site.partials.sections')
<div class="wrap">
  <a class="nominate" href="{{ $p->label('cta_url') ?: url('/contact') }}">
    <span class="plus" aria-hidden="true">+</span>
    <b>{{ $p->label('cta_title', __('site.inspiring.cta_title')) }}</b>
    <small>{{ $p->label('cta_text', __('site.inspiring.cta_text')) }}</small>
    <span class="btn">{{ $p->label('cta_button', __('site.inspiring.cta_button')) }}</span>
  </a>
</div>
</x-site.layout>
