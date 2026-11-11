{{-- Design: federation.html (js/federation.js rendered here; the gallery "رواق الاتحاد" by js fed-gallery). --}}
@php
    $o = $organization;
    $presidents = $o->leaders->where('kind', 'president')->values();
    $current = $presidents->firstWhere('is_current', true);
    $memberships = collect($o->memberships ?? [])->map(fn ($m) => ['name' => \App\Support\Localized::get($m['name'] ?? null), 'year' => $m['year'] ?? null]);
    $facts = array_filter([
        __('site.federation.founded') => $o->founded,
        __('site.federation.declared') => $o->declared,
        __('site.federation.hq') => $o->hq,
        __('site.federation.president') => $current?->name,
    ]);
    $people = $o->achievers()->published()->with('media')->withCount(['achievements' => fn ($q) => $q->published()])->get()->unique('id')->values();
    $photos = $o->getMedia('gallery');
    $hasGallery = $photos->isNotEmpty() || $o->videos->isNotEmpty();
    $plural = fn ($n, $noun) => \App\Support\ArabicCount::of($n, $noun);
@endphp
<x-site.layout :title="$o->name" :description="\Illuminate\Support\Str::limit(strip_tags((string) $o->about), 160)" :image="$o->logoUrl()"
    styles="federation" body-class="flush" :noindex="! $o->isLive()" class="fp-page" data-fed-page
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index'), 'sub' => $o->name]">
  <section class="fp-hero">
    @if ($logo = $o->logoUrl())<span class="fp-logo"><img src="{{ $logo }}" alt="{{ __('site.federation.logo_of', ['name' => $o->name]) }}"></span>@endif
    <span class="fp-type">{{ $o->type->label }}@if ($o->founded) – {{ __('site.federation.founded_in', ['year' => $o->founded]) }}@endif</span>
    <h1>{{ $o->name }}</h1>
    @if ($en = $o->getTranslation('name', 'en', false))<p class="fp-en" dir="ltr">{{ $en }}@if ($o->abbr) ({{ $o->abbr }})@endif</p>@endif
    @if ($memberships->isNotEmpty())<div class="fp-chips">@foreach ($memberships as $m)<span>{{ preg_replace('/ \(.*\)/u', '', $m['name']) }} <b>{{ $m['year'] }}</b></span>@endforeach</div>@endif
    <nav class="fp-jump" aria-label="{{ __('site.university.jump') }}"><a href="#fp-about">{{ __('site.federation.about') }}</a>
      @if ($presidents->isNotEmpty())<a href="#fp-pres">{{ __('site.university.jump_presidents') }}</a>@endif
      @if ($hasGallery)<a href="#fp-gal">{{ __('site.federation.gallery_short') }}</a>@endif
      <a href="#fp-ach">{{ __('site.university.jump_achievements') }}</a><a href="#fp-people">{{ __('site.university.jump_people') }}</a></nav>
  </section>
  <div class="wrap">
    <div class="fp-top">
      <section class="fp-sec" id="fp-about" aria-labelledby="fp-about-h"><h2 class="fp-h" id="fp-about-h">{{ __('site.federation.about') }}</h2><div>
        @if ($o->about)
          @if ($facts)<dl class="fp-facts">@foreach ($facts as $label => $value)<div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>@endif
          <p class="fp-text">{{ $o->about }}</p>
          @if ($memberships->isNotEmpty())<ul class="fp-members">@foreach ($memberships as $m)<li><b>{{ $m['year'] }}</b>{{ $m['name'] }}</li>@endforeach</ul>@endif
          @if ($o->website)<a class="fp-site" href="{{ $o->website }}" target="_blank" rel="noopener">{{ __('site.federation.website') }} <span aria-hidden="true">↗</span></a>@endif
        @else
          <p class="fp-soon">{{ __('site.federation.soon') }}</p>
        @endif
      </div></section>
      @if ($presidents->isNotEmpty())
        <section class="fp-sec" id="fp-pres" aria-labelledby="fp-pres-h"><h2 class="fp-h" id="fp-pres-h">{{ __('site.federation.presidents') }}</h2><ol class="fp-tl">
          @foreach ($presidents->reverse() as $p)
            @php $photo = \App\Support\Media\Src::of($p->image('photo')); @endphp
            <li @class(['is-now' => $p->is_current])><span class="fp-pt"><span class="fp-yrs">{{ $p->from_year }} – {{ $p->to_year ?: __('site.university.now') }}</span><b>{{ $p->name }}</b>
              @if ($p->note)<span class="fp-note">{{ $p->note }}</span>@endif @if ($p->is_current)<span class="fp-now">{{ __('site.federation.president') }}</span>@endif</span>
              <span @class(['fp-pp', 'is-empty' => ! $photo])>@if ($photo)<img src="{{ $photo }}" alt="{{ $p->name }}" loading="lazy">@else<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/></svg>@endif</span></li>
          @endforeach
        </ol></section>
      @endif
    </div>

    @if ($hasGallery)
      <section class="fp-sec fp-gal" id="fp-gal" aria-labelledby="fp-gal-h" data-fp-gal>
        <div class="gal-head"><span class="gal-eyebrow">{{ __('site.university.library_eyebrow') }}</span><h2 id="fp-gal-h">{{ __('site.federation.gallery') }}</h2>
          <p>{{ $o->gallery_intro }}</p><span class="gal-sum" data-gal-sum></span></div>
        <div class="gal-tabs" role="tablist" aria-label="{{ __('site.federation.gallery_tabs') }}" data-gal-tabs></div>
        <div class="gal-eras" aria-label="{{ __('site.federation.eras') }}" data-gal-eras></div>
        <div class="gal-grid" data-gal-grid></div>
      </section>
      @php
          $gallery = [
              'photos' => $photos->map(fn ($m) => [
                  'src' => \App\Support\Media\Src::of($m), 'kind' => $m->getCustomProperty('kind', 'photo'),
                  'era' => $m->getCustomProperty('era'), 'year' => $m->getCustomProperty('year'),
                  'title' => $m->getCustomProperty('title') ?? \App\Support\Localized::get($m->getCustomProperty('caption')), 'note' => $m->getCustomProperty('note'),
                  'credit' => $m->getCustomProperty('credit') ? [$m->getCustomProperty('credit'), $m->getCustomProperty('credit_url') ?? ''] : null,
              ])->values()->all(),
              'videos' => $o->videos->map(fn ($v) => ['yt' => $v->youtube_id, 'era' => $v->era, 'year' => $v->year, 'title' => $v->title, 'note' => $v->note])->values()->all(),
          ];
      @endphp
      <script>window.INJAZ_FED_GALLERY = {!! json_encode($gallery, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
    @endif

    <section class="fp-sec" id="fp-ach" aria-labelledby="fp-ach-h">
      <h2 class="fp-h" id="fp-ach-h">{{ __('site.federation.achievements') }} @if ($achievements->isNotEmpty())<small>{{ $plural($achievements->count(), 'achievements') }}</small>@endif</h2>
      <div class="fp-cards">
        @forelse ($achievements->take(6) as $row)
          @php $a = $row['achievement']; @endphp
          <a class="fp-card" href="{{ $a->url() }}"><span class="fp-img">@if ($img = $a->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achievement::focal($a->image('cover')) }}">@endif</span>
            <span class="fp-cb"><b>{{ $a->title }}</b><small>{{ collect([$a->primaryAchiever()?->name ?? $o->name, $a->dateLabel()])->filter()->implode(' · ') }}</small></span></a>
        @empty
          <p class="fp-soon">{{ __('site.federation.no_achievements') }}</p>
        @endforelse
      </div>
      @if ($achievements->isNotEmpty())<a class="fp-more" href="{{ route('organizations.achievements', $o) }}">{{ __('site.federation.all_achievements') }} ({{ \App\Support\ArabicDate::digits($achievements->count()) }}) <span aria-hidden="true">←</span></a>@endif
    </section>

    @if ($o->milestones->isNotEmpty())
      <section class="fp-sec" aria-labelledby="fp-ms-h"><h2 class="fp-h" id="fp-ms-h">{{ __('site.federation.milestones') }}</h2><ul class="fp-ms">
        @foreach ($o->milestones as $m)<li><span class="fp-y">{{ $m->year }}</span><span><b>{{ $m->title }}</b>@if ($m->place)<small>{{ $m->place }}</small>@endif</span></li>@endforeach
      </ul></section>
    @endif

    <section class="fp-sec" id="fp-people" aria-labelledby="fp-ppl-h">
      <h2 class="fp-h" id="fp-ppl-h">{{ __('site.federation.people') }} @if ($people->isNotEmpty())<small>{{ $plural($people->count(), 'achievers') }}</small>@endif</h2>
      <div class="fp-people">
        @forelse ($people as $person)
          <a class="fp-person" href="{{ $person->url() }}"><span class="fp-ph">@if ($img = $person->imageUrl('portrait', 'card'))<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achiever::focal($person->image('portrait')) }}">@endif</span>
            <b>{{ $person->name }}</b><small>{{ collect([$person->headline, $plural((int) $person->achievements_count, 'achievements')])->filter()->implode(' · ') }}</small></a>
        @empty
          <p class="fp-soon">{{ __('site.federation.no_people') }}</p>
        @endforelse
      </div>
      @if ($people->isNotEmpty())<a class="fp-more" href="{{ route('organizations.people', $o) }}">{{ __('site.federation.all_people') }} <span aria-hidden="true">←</span></a>@endif
    </section>

    @if ($o->sources->isNotEmpty())
      <p class="fp-src">{{ __('site.university.sources') }} @foreach ($o->sources as $source)@if ($source->url)<a href="{{ $source->url }}" target="_blank" rel="noopener">{{ $source->title }}</a>@else{{ $source->title }}@endif{{ $loop->last ? '' : ' · ' }}@endforeach</p>
    @endif
  </div>
</x-site.layout>
