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
      @if ($o->milestones->isNotEmpty())<a href="#fp-ms">{{ __('site.federation.milestones') }}</a>@endif
      <a href="#fp-ach">{{ __('site.university.jump_achievements') }}</a>
      @if ($hasGallery)<a href="#fp-gal">{{ __('site.federation.gallery_short') }}</a>@endif<a href="#fp-people">{{ __('site.university.jump_people') }}</a></nav>
  </section>
  <div class="wrap">
    {{-- «عن الاتحاد» in one framed panel (facts ribbon, text, memberships as badges, website), then «رؤساء الاتحاد» as the
         animated leadership stage (x-site.organization.heads-stage) instead of a long list (css federation-pro). --}}
    <section class="fp-sec fa" id="fp-about" aria-labelledby="fp-about-h"><h2 class="fp-h" id="fp-about-h">{{ __('site.federation.about') }}</h2>
      @if ($o->about)
        <div class="fa-panel">
          @if ($facts)<dl class="fa-facts">@foreach ($facts as $label => $value)<div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>@endif
          <div class="fa-body">
            <p class="fa-text">{{ $o->about }}</p>
            @if ($memberships->isNotEmpty() || $o->website)
              <div class="fa-side">
                @if ($memberships->isNotEmpty())<h3>{{ __('site.federation.memberships') }}</h3>
                  <ul class="fa-members">@foreach ($memberships as $m)<li><b>{{ $m['year'] }}</b><span>{{ $m['name'] }}</span></li>@endforeach</ul>@endif
                @if ($o->website)<a class="fa-site" href="{{ $o->website }}" target="_blank" rel="noopener">{{ __('site.federation.website') }} <span aria-hidden="true">↗</span></a>@endif
              </div>
            @endif
          </div>
        </div>
      @else
        <p class="fp-soon">{{ __('site.federation.soon') }}</p>
      @endif
    </section>
    @if ($presidents->isNotEmpty())
      <section class="fp-sec fa-pres" id="fp-pres" aria-labelledby="fp-pres-h"><h2 class="fp-h" id="fp-pres-h">{{ __('site.federation.presidents') }} <small>{{ \App\Support\ArabicDate::digits($presidents->count()) }}</small></h2>
        <x-site.organization.heads-stage :heads="$presidents->sortBy(fn ($p) => [$p->from_year ?? 9999, $p->sort_order])->values()" />
      </section>
    @endif




    @if ($o->milestones->isNotEmpty())
      {{-- «أبرز المحطات» on a pitch: every stop's card is shown at once, in time order; a ball rolls under them from stop to stop
           and lights each card in turn (js sections/fed-pro). --}}
      @php $stops = $o->milestones->sortBy(fn ($m) => (int) $m->year)->values(); @endphp
      <section class="fp-sec fms" id="fp-ms" aria-labelledby="fp-ms-h" data-fms>
        <h2 class="fp-h" id="fp-ms-h">{{ __('site.federation.milestones') }}</h2>
        <div class="fms-pitch" style="--n: {{ $stops->count() }}">
          <span class="fms-lines" aria-hidden="true"></span>
          <div class="fms-scroll">
          <div class="fms-cards">
            @foreach ($stops as $m)
              <article @class(['fms-card', 'on' => $loop->last]) style="--i: {{ $loop->index }}">
                <span class="fms-cup" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M24 31c-5.5 0-10-4.5-10-10V8h20v13c0 5.5-4.5 10-10 10Z"/><path d="M14 12H8v4a7 7 0 0 0 7 7M34 12h6v4a7 7 0 0 1-7 7M24 31v7M16 42h16M19 38h10"/></svg></span>
                <b class="fms-year">{{ \App\Support\ArabicDate::digits($m->year) }}</b>
                <h3>{{ $m->title }}</h3>@if ($m->place)<p>{{ $m->place }}</p>@endif
              </article>
            @endforeach
          </div>
          <div class="fms-track" role="tablist" aria-label="{{ __('site.federation.milestones') }}" style="--n: {{ $stops->count() }}">
            <span class="fms-path" aria-hidden="true"><i></i></span>
            <span class="fms-ball" aria-hidden="true"><svg viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="#fff" stroke="#14282A" stroke-width="1.6"/><path d="M20 11l7 5-3 8h-8l-3-8z" fill="#14282A"/><path d="M20 11V3M27 16l7-3M24 24l5 7M16 24l-5 7M13 16l-7-3" stroke="#14282A" stroke-width="1.4"/></svg></span>
            @foreach ($stops as $m)
              <button type="button" role="tab" @class(['fms-stop', 'on' => $loop->last]) aria-selected="{{ $loop->last ? 'true' : 'false' }}" style="--i: {{ $loop->index }}"><i></i><span>{{ \App\Support\ArabicDate::digits($m->year) }}</span></button>
            @endforeach
          </div>
          </div>
        </div>
      </section>
    @endif

    <section class="fp-sec" id="fp-ach" aria-labelledby="fp-ach-h">
      <h2 class="fp-h" id="fp-ach-h">{{ __('site.federation.achievements') }} @if ($achievements->isNotEmpty())<small>{{ $plural($achievements->count(), 'achievements') }}</small>@endif</h2>
      {{-- Cards like the ministries' achievements (eight at most). --}}
      <div class="fa-grid">
        @forelse ($achievements->take(8) as $row)
          @php $a = $row['achievement']; $img = $a->imageUrl('cover', 'card') ?? $a->primaryAchiever()?->imageUrl('portrait', 'card'); @endphp
          <a class="fa-card" href="{{ $a->url() }}"><span class="fa-img">@if ($img)<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achievement::focal($a->image('cover')) }}">@endif</span>
            @if ($tag = $a->disciplineLabel())<span class="fa-pill">{{ $tag }}</span>@endif<b>{{ $a->title }}</b>
            <small>{{ collect([$a->primaryAchiever()?->name ?? $o->name, $a->dateLabel()])->filter()->implode(' · ') }}</small></a>
        @empty
          <p class="fp-soon">{{ __('site.federation.no_achievements') }}</p>
        @endforelse
      </div>
      @if ($achievements->isNotEmpty())<a class="fp-more" href="{{ route('organizations.achievements', $o) }}">{{ __('site.federation.all_achievements') }} ({{ \App\Support\ArabicDate::digits($achievements->count()) }}) <span aria-hidden="true">←</span></a>@endif
    </section>

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
