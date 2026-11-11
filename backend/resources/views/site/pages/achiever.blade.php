{{-- Design: profile.html / profile-ali-alhabsi.html (one template for every achiever). --}}
@php
    $cutout = $achiever->image('cutout');
    $portrait = $achiever->image('portrait');
    $heroImage = $achiever->imageUrl('cutout') ?? $achiever->imageUrl('portrait');
    $avatar = $achiever->imageUrl('avatar') ?? $achiever->imageUrl('portrait', 'card');
    $general = app(\App\Settings\GeneralSettings::class);
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $pct = fn ($n) => $stats['total'] ? $d(round($n / $stats['total'] * 100)).'٪' : $d(0).'٪';
    $years = $achievements->map(fn ($a) => $a->achieved_at?->year)->filter()->unique()->sortDesc()->values();
    $scopes = $achievements->pluck('scope')->filter()->unique('id')->sortBy('sort_order')->values();
@endphp
<x-site.layout :title="$achiever->name" :description="$achiever->short_bio ?: $achiever->headline" :image="$achiever->imageUrl('portrait')" styles="achiever"
    body-class="flush achiever-profile" og-type="profile" :noindex="! $achiever->isLive()"
    :crumb="['title' => $listPage->title, 'url' => route('achievers.index'), 'sub' => $achiever->name]">
  <x-slot:head>
    <script type="application/ld+json">@php $jsonData = array_filter([
      '@context' => 'https://schema.org', '@type' => 'Person', 'name' => $achiever->name,
      'alternateName' => $achiever->getTranslation('name', 'en', false) ?: null, 'description' => $achiever->short_bio ?: null,
      'image' => $achiever->imageUrl('portrait'), 'url' => $achiever->url(),
      'sameAs' => $achiever->socialLinks->pluck('url')->filter()->values()->all() ?: null,
    ]); @endphp
{!! json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
  </x-slot:head>

  <section class="profile-hero">
    <div class="top">
      <div class="profile-portrait hm">
        @if ($achiever->portrait_trace)
          <svg class="hm-trace" viewBox="0 0 948 928" preserveAspectRatio="none" aria-hidden="true"><defs><linearGradient id="hmGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F6E3B4"/><stop offset=".5" stop-color="#D9B262"/><stop offset="1" stop-color="#A88344"/></linearGradient></defs><path class="hm-line" pathLength="100" d="{{ $achiever->portrait_trace }}"/><path class="hm-run" pathLength="100" d="{{ $achiever->portrait_trace }}"/><path class="hm-run hm-run-2" pathLength="100" d="{{ $achiever->portrait_trace }}"/></svg>
        @endif
        @if ($heroImage)
          <img class="photo" src="{{ $heroImage }}" data-avatar="{{ $avatar }}" alt="{{ \App\Models\Achiever::alt($cutout ?? $portrait, $achiever->name) }}" width="948" height="928">
        @endif
      </div>
      <div class="txt">
        <div class="who">
          @if ($achiever->is_featured)
            <span class="featured-mark" aria-label="{{ __('site.achievers.featured') }}"><svg viewBox="0 0 64 64" aria-hidden="true"><path fill="#C6A15A" d="M32 6L40.4 11.7 50.4 13.6 52.3 23.6 58 32 52.3 40.4 50.4 50.4 40.4 52.3 32 58 23.6 52.3 13.6 50.4 11.7 40.4 6 32 11.7 23.6 13.6 13.6 23.6 11.7Z"/><path d="M23 33.2 29.2 40 42 25" fill="none" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
          @endif
          <h1>{{ $achiever->name }}</h1>
          @if ($english = $achiever->getTranslation('name', 'en', false))<p class="en" lang="en">{{ $english }}</p>@endif
        </div>
        @if ($lines = $achiever->heroLines())
          <p class="about">{!! collect($lines)->map(fn ($l) => e($l))->implode('<br>') !!}</p>
        @endif
      </div>
    </div>
  </section>

  <section class="section wrap profile-contact" aria-label="{{ __('site.achievers.contacts') }}">
    <div class="profile-contact-row">
      <button type="button" class="id-launch" data-idcard-open aria-haspopup="dialog" aria-controls="idcard"><span class="id-mini" aria-hidden="true"><i></i></span><span>{{ __('site.achievers.id_card') }}</span><span class="id-arrow" aria-hidden="true">‹</span></button>
      @if ($achiever->socialLinks->whereNotNull('url')->isNotEmpty())
        <div class="profile-socials">
          @foreach ($achiever->socialLinks->whereNotNull('url') as $social)
            <a href="{{ $social->url }}" class="profile-social" target="_blank" rel="noopener" aria-label="{{ $social->label() }} — {{ ltrim((string) $social->handle, '@') }}">
              <span class="profile-social-icon">@if (in_array($social->platform, ['instagram', 'x', 'snapchat', 'linkedin', 'youtube'], true))<img src="{{ Vite::asset('resources/site/assets/svg/icon-'.$social->platform.'.svg') }}" alt="" width="15" height="15">@else<x-site.social-icon :platform="$social->platform" />@endif</span>@if ($social->handle)<span class="profile-social-handle" dir="ltr">{{ '@'.ltrim($social->handle, '@') }}</span>@endif
            </a>
          @endforeach
        </div>
      @endif
      <button type="button" class="profile-share" data-profile-share aria-label="{{ __('site.achievers.share') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V4"/><path d="m8 7 4-4 4 4"/><path d="M6 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-1"/></svg>
      </button>
    </div>
  </section>

  <section class="section wrap profile-story-section">
    @if ($achiever->story)
      <div class="card story" data-story>
        <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2>{{ __('site.achievers.story') }}</h2></div>
        <p id="story-text">{!! nl2br(e($achiever->story)) !!}</p>
        <button class="toggle" type="button" aria-expanded="false" aria-controls="story-text" data-more="{{ __('site.achievers.read_more') }}" data-less="{{ __('site.achievers.hide') }}"><span>{{ __('site.achievers.read_more') }}</span><img src="{{ Vite::asset('resources/site/assets/svg/icon-chevron-down.svg') }}" alt=""></button>
      </div>
    @endif
    @if ($stats['total'])
      <div class="medal-stats" data-medal-stats data-total="{{ $stats['total'] }}" data-intl="{{ $stats['international'] }}" data-local="{{ $stats['local'] }}" role="group"
           aria-label="{{ __('site.achievers.medals_label', ['total' => $d($stats['total']), 'intl' => $d($stats['international']), 'local' => $d($stats['local'])]) }}">
        <div class="ms-medal">
          <div class="ms-badge">
            <svg class="ms-svg" viewBox="0 0 200 200" aria-hidden="true"></svg>
            <div class="ms-core"><strong data-ms-count>{{ $d($stats['total']) }}</strong></div>
          </div>
          <span class="ms-label">{{ __('site.achievers.medals_total') }}</span>
        </div>
        <div class="ms-legend">
          <button type="button" class="ms-row" data-kind="intl" aria-pressed="false" aria-label="{{ __('site.achievers.medals_row', ['n' => $d($stats['international']), 'kind' => __('site.achievers.medals_intl'), 'total' => $d($stats['total'])]) }}">
            <span class="ms-head"><svg class="ms-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" aria-hidden="true"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4.5" ry="9"/><path d="M3 12h18M4.5 7.5h15M4.5 16.5h15"/></svg><strong class="ms-num" data-ms-num="{{ $stats['international'] }}">{{ $d($stats['international']) }}</strong><span class="ms-txt">{{ __('site.achievers.medals_intl') }}</span><span class="ms-pct" data-ms-pct="intl">{{ $pct($stats['international']) }}</span></span>
            <span class="ms-beads" data-ms-beads="intl" aria-hidden="true"></span>
          </button>
          <button type="button" class="ms-row" data-kind="local" aria-pressed="false" aria-label="{{ __('site.achievers.medals_row', ['n' => $d($stats['local']), 'kind' => __('site.achievers.medals_local'), 'total' => $d($stats['total'])]) }}">
            <span class="ms-head"><img class="ms-ico" src="{{ Vite::asset('resources/site/assets/svg/icon-oman-map.svg') }}" alt="" width="21" height="23"><strong class="ms-num" data-ms-num="{{ $stats['local'] }}">{{ $d($stats['local']) }}</strong><span class="ms-txt">{{ __('site.achievers.medals_local') }}</span><span class="ms-pct" data-ms-pct="local">{{ $pct($stats['local']) }}</span></span>
            <span class="ms-beads" data-ms-beads="local" aria-hidden="true"></span>
          </button>
        </div>
      </div>
    @endif
  </section>

  @if ($achiever->journeyItems->isNotEmpty())
    <section class="section wrap" id="journey" aria-labelledby="journey-title">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="journey-title">{{ __('site.achievers.journey') }}</h2></div>
      <div class="gj" data-journey-gold>
        <span class="gj-halo" aria-hidden="true"></span>
        @if ($journeyPhoto = $achiever->imageUrl('journey'))
          <img class="gj-ph" src="{{ $journeyPhoto }}" alt="{{ \App\Models\Achiever::alt($achiever->image('journey'), $achiever->name) }}" decoding="async">
        @endif
        @if ($achiever->journey_kicker || $achiever->journey_title)
          <div class="gj-head"><p class="gj-lead">@if ($achiever->journey_kicker)<small>{{ $achiever->journey_kicker }}</small>@endif @if ($achiever->journey_title)<strong>{!! preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e($achiever->journey_title)) !!}</strong>@endif</p></div>
        @endif
        <ol class="gj-list">
          @foreach ($achiever->journeyItems as $item)
            <li data-icon="{{ $item->icon }}"><span class="gj-t">{{ $item->title }}</span><b class="gj-y">{{ $item->year }}</b></li>
          @endforeach
        </ol>
      </div>
    </section>
  @endif

  @if ($achievements->isNotEmpty())
    <section class="section wrap" id="achievements">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2>{{ __('site.achievers.achievements') }}</h2></div>
      <div class="sort-row">
        <div class="chips-flow" data-chips>
          <button type="button" class="chip active" aria-pressed="true" data-scope-filter="all"><span>{{ __('site.achievers.scope_all') }}</span></button><button type="button" class="chip" aria-pressed="false" data-scope-filter="international"><span>{{ __('site.achievers.scope_international') }}</span></button><button type="button" class="chip" aria-pressed="false" data-scope-filter="local"><span>{{ __('site.achievers.scope_local') }}</span></button>
        </div>
        <button type="button" class="chip filter-toggle" aria-expanded="false" aria-controls="filter-panel" data-profile-sort-toggle>
          <img src="{{ Vite::asset('resources/site/assets/svg/icon-filter.svg') }}" alt=""><span>{{ __('site.achievers.sort') }}</span></button>
      </div>
      <div class="filter-panel" id="filter-panel" data-profile-sort-panel
           data-years='@json($years->map(fn ($y) => ['v' => (string) $y, 'l' => $d($y)])->all())'
           data-types='@json($scopes->map(fn ($s) => ['v' => $s->slug, 'l' => $s->name])->all())'
           data-all="{{ __('site.all') }}">
        <div class="fp-inner">
          <div class="f-boxes f-boxes-2"><button type="button" class="f-box" data-key="year" aria-expanded="false"><span class="f-label">{{ __('site.achievers.sort_year') }}</span><span class="f-val">{{ __('site.all') }}</span><svg class="chev" viewBox="0 0 14 8" aria-hidden="true"><path d="M1 1l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button type="button" class="f-box" data-key="type" aria-expanded="false"><span class="f-label">{{ __('site.achievers.sort_type') }}</span><span class="f-val">{{ __('site.all') }}</span><svg class="chev" viewBox="0 0 14 8" aria-hidden="true"><path d="M1 1l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
          <div class="f-sheet" data-sheet hidden></div>
          <button type="button" class="f-reset" data-filter-reset>{{ __('site.achievers.sort_reset') }}</button>
        </div>
      </div>
      <div class="grid-2 cols-3" style="margin-top:16px" data-ach-grid>
        @foreach ($achievements as $achievement)
          <x-site.cards.achievement :achievement="$achievement" variant="profile" />
        @endforeach
      </div>
      <p class="ach-empty" data-ach-empty hidden>{{ __('site.achievers.no_achievements') }}</p>
    </section>
  @endif

  @if ($achiever->quotes->isNotEmpty())
    <section class="section wrap" id="said" aria-labelledby="said-title">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="said-title">{{ __('site.achievers.said', ['name' => $achiever->name]) }}</h2></div>
      <div class="said">
        @foreach ($achiever->quotes as $quote)
          <figure class="said-card" style="--i:{{ $loop->index }}">
            <span class="said-av" aria-hidden="true"><img src="{{ $quote->imageUrl('avatar', 'card') ?? Vite::asset('resources/site/assets/svg/icon-omani-man-teal.svg') }}" alt="" loading="lazy"></span>
            <div class="said-body">
              <blockquote>{{ $quote->text }}</blockquote>
              <figcaption><b>{{ $quote->name }}</b>@if ($quote->role)<small>{{ $quote->role }}</small>@endif</figcaption>
            </div>
          </figure>
        @endforeach
      </div>
      @if ($achiever->quotes_note)<p class="said-note">{{ $achiever->quotes_note }}</p>@endif
    </section>
  @endif

  @if ($achiever->personal_quote)
    <section class="section wrap">
      <x-site.pull-quote :text="$achiever->personal_quote" :by="$achiever->name" :role="$achiever->personal_quote_context" :note="$achiever->personal_quote_source" />
    </section>
  @endif

  @if (($gallery = $achiever->getMedia('gallery'))->isNotEmpty())
    <section class="section wrap">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2>{{ __('site.achievers.gallery') }}</h2></div>
      <div class="media3" data-gallery>
        @foreach ($gallery as $image)
          @php $caption = \App\Support\Localized::get($image->getCustomProperty('caption')); @endphp
          <a href="{{ \App\Support\Media\Src::of($image) }}" data-caption="{{ $caption }}" aria-label="{{ __('site.achievers.view_image', ['caption' => $caption]) }}"><img class="cover" src="{{ $image->hasGeneratedConversion('card') ? $image->getUrl('card') : \App\Support\Media\Src::of($image) }}" alt="{{ $caption }}" loading="lazy" style="object-position:{{ \App\Models\Achiever::focal($image) }}"><span class="ico-circle"><img src="{{ Vite::asset('resources/site/assets/svg/icon-images.svg') }}" alt=""></span></a>
        @endforeach
      </div>
    </section>
  @endif

  @if ($related->isNotEmpty())
    <section class="section gray section-pad" aria-labelledby="related-title">
      <div class="wrap">
        <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="related-title">{{ __('site.achievers.related') }}</h2></div>
        <div class="rel rel-original rel-moving" data-related><div class="rel-track"><div class="rel-group">
          @foreach ($related as $other)
            <a class="rel-card" href="{{ $other->url() }}"><img class="rel-real-photo" src="{{ $other->imageUrl('portrait', 'card') ?? Vite::asset('resources/site/assets/svg/icon-omani-'.($other->isFemale() ? 'woman' : 'man').'-teal.svg') }}" alt="{{ $other->name }}" width="100" height="100" loading="lazy" style="object-position:{{ \App\Models\Achiever::focal($other->image('portrait')) }}"><b>{{ $other->name }}</b>@if ($other->headline)<small>{{ $other->headline }}</small>@endif</a>
          @endforeach
        </div></div></div>
        @if ($achiever->field)
          <a class="rel-all" href="{{ route('achievers.index', ['field' => $achiever->field->slug]) }}">{{ __('site.achievers.related_all', ['field' => $achiever->field->name]) }} <span aria-hidden="true">←</span></a>
        @endif
      </div>
    </section>
  @endif

  <x-slot:scripts>
    <dialog class="idcard-dialog" id="idcard" aria-labelledby="idcard-name">
      <div class="idcard-stage">
        <script type="application/json" id="member-card-data">@php $jsonData = [
          'name' => $achiever->name,
          'name_en' => $achiever->getTranslation('name', 'en', false) ?: null,
          'field' => (string) $achiever->field?->name,
          'tone' => $achiever->field?->card_tone ?? 'culture',
          'photo' => $heroImage,
          'bio' => (string) ($achiever->short_bio ?: $achiever->headline),
          'stats' => [
            ['value' => (string) $stats['total'], 'label' => __('site.achievers.card_stat')],
            ['value' => (string) $stats['international'], 'label' => __('site.achievers.card_intl')],
            ['value' => (string) $stats['local'], 'label' => __('site.achievers.card_local')],
          ],
          'international' => $achievements->filter(fn ($a) => $a->scope?->is_international)->take(4)->pluck('title')->values(),
          'local' => $achievements->reject(fn ($a) => $a->scope?->is_international)->take(3)->pluck('title')->values(),
          'site' => $general->t('site_name'),
          'logo' => \App\Support\Site\SiteMedia::url($general->logo, 'svg/logo.svg'),
          'mark' => Vite::asset('resources/site/assets/svg/watermark.svg'),
        ]; @endphp
{!! json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
        <article class="idcard" id="idcard-sheet" data-tone="culture"></article>
        <div class="idcard-side">
          <button type="button" data-idcard-pdf><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h4"/></svg></span>{{ __('site.achievers.pdf') }}</button>
          <button type="button" data-idcard-image><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-9 9"/></svg></span>{{ __('site.achievers.image') }}</button>
          <button type="button" data-idcard-share><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15V4"/><path d="m8 7 4-4 4 4"/><path d="M6 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-1"/></svg></span>{{ __('site.achievers.share') }}</button>
          <button type="button" data-idcard-close><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></span>{{ __('site.achievers.close') }}</button>
          <p class="idcard-note" data-idcard-note hidden></p>
        </div>
      </div>
    </dialog>
  </x-slot:scripts>
</x-site.layout>
