{{-- Design: profile.html / profile-ali-alhabsi.html (one template for every achiever). --}}
@php
    $cutout = $achiever->image('cutout');
    $portrait = $achiever->image('portrait');
    $heroImage = $achiever->imageUrl('cutout') ?? $achiever->imageUrl('portrait');
    $avatar = $achiever->headerAvatarUrl();
    $general = app(\App\Settings\GeneralSettings::class);
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $pct = fn ($n) => $stats['total'] ? $d(round($n / $stats['total'] * 100)).'٪' : $d(0).'٪';
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
        @if ($heroImage)
          <img class="photo" src="{{ $heroImage }}" data-avatar="{{ $avatar }}" alt="{{ \App\Models\Achiever::alt($cutout ?? $portrait, $achiever->name) }}" width="948" height="928">
        @endif
      </div>
      <div class="txt">
        <div class="who">
          <div class="who-name">
            <h1>{{ $achiever->name }}</h1>
            @if ($achiever->is_featured)
              <span class="featured-mark" role="img" aria-label="{{ __('site.achievers.featured') }}"><svg viewBox="0 0 64 64" aria-hidden="true"><path fill="#C6A15A" d="M32 6L40.4 11.7 50.4 13.6 52.3 23.6 58 32 52.3 40.4 50.4 50.4 40.4 52.3 32 58 23.6 52.3 13.6 50.4 11.7 40.4 6 32 11.7 23.6 13.6 13.6 23.6 11.7Z"/><path d="M23 33.2 29.2 40 42 25" fill="none" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            @endif
          </div>
          @if ($english = $achiever->getTranslation('name', 'en', false))<p class="en" lang="en">{{ $english }}</p>@endif
        </div>
        @if ($lines = $achiever->heroLines())
          <p class="about">{{ implode(' - ', $lines) }}</p>
        @endif
      </div>
    </div>
  </section>
  {{-- The portrait rises from its line and the name, English name and introduction rise with it (profile-portrait.js).
       Set before the first paint so nothing jumps; shown without motion after 4 s if the photo is slow. --}}
  <script>(function (h) { var f = h.querySelector('.profile-portrait'); if (!f || !f.querySelector('.photo') || matchMedia('(prefers-reduced-motion: reduce)').matches) return; h.classList.add('hero-wait'); f.classList.add('motion-pending'); setTimeout(function () { if (!h.classList.contains('hero-wait')) return; f.classList.remove('motion-pending'); h.classList.remove('hero-wait'); h.classList.add('hero-in'); }, 4000); })(document.currentScript.previousElementSibling);</script>

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
        {{-- Paragraphs: the story written as one block is split at sentence ends (App\Support\Paragraphs). --}}
        <p id="story-text">{!! nl2br(e(\App\Support\Paragraphs::text((string) $achiever->story))) !!}</p>
        <button class="toggle" type="button" aria-expanded="false" aria-controls="story-text" data-more="{{ __('site.achievers.read_more') }}" data-less="{{ __('site.achievers.hide') }}"><span>{{ __('site.achievers.read_more') }}</span><img src="{{ Vite::asset('resources/site/assets/svg/icon-chevron-down.svg') }}" alt=""></button>
      </div>
    @endif
    @if ($stats['total'] && $profileItems)
      @php
        $span = \App\Support\Site\ProfileAchievements::span($profileItems);
        $spanLine = collect([
            $span['from'] ? $d($span['from']).($span['to'] !== $span['from'] ? ' – '.$d($span['to']) : '') : null,
            $span['countries'] ? \App\Support\ArabicCount::of($span['countries'], 'countries') : null,
        ])->filter()->implode(' · ');
        $intlShare = (int) round($stats['international'] * 100 / $stats['total']);
      @endphp
      {{-- Design 9: replaced on load by the stats bar (js/aj-pro.js), then by the medal beside the story (js/aj-split.js). --}}
      <div class="kc" data-kc aria-label="{{ __('site.achievers.stats_label') }}">
        <button type="button" class="kc-c t" data-st="all" style="--d:0">
          <span class="kc-ring"><svg viewBox="0 0 120 120" aria-hidden="true"><circle class="bg" cx="60" cy="60" r="52"/><circle class="fg i" cx="60" cy="60" r="52" pathLength="100" style="--p:{{ $intlShare }}"/><circle class="fg l" cx="60" cy="60" r="52" pathLength="100" style="--p:{{ 100 - $intlShare }};--o:{{ $intlShare }}"/></svg><b data-to="{{ $stats['total'] }}">{{ $d($stats['total']) }}</b></span>
          <span class="kc-tx"><em>{{ __('site.achievers.medals_total') }}</em>@if ($spanLine !== '')<small>{{ $spanLine }}</small>@endif</span>
          <span class="kc-shine" aria-hidden="true"></span>
        </button>
        <button type="button" class="kc-c i" data-st="intl" style="--d:1">
          <span class="kc-ic"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#096770" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.6 2.6 4 5.6 4 9s-1.4 6.4-4 9c-2.6-2.6-4-5.6-4-9s1.4-6.4 4-9z"/></svg></span>
          <span class="kc-tx"><span class="kc-n"><b data-to="{{ $stats['international'] }}">{{ $d($stats['international']) }}</b><i data-pct="{{ $intlShare }}">{{ $d($intlShare) }}٪</i></span><em>{{ __('site.achievers.medals_intl') }}</em>
          <span class="kc-bar"><span style="--w:{{ $intlShare }}%"></span></span></span>
          <span class="kc-go" aria-hidden="true">←</span>
        </button>
        <button type="button" class="kc-c l" data-st="local" style="--d:2">
          <span class="kc-ic"><img src="{{ Vite::asset('resources/site/assets/svg/icon-oman-map.svg') }}" alt=""></span>
          <span class="kc-tx"><span class="kc-n"><b data-to="{{ $stats['local'] }}">{{ $d($stats['local']) }}</b><i data-pct="{{ 100 - $intlShare }}">{{ $d(100 - $intlShare) }}٪</i></span><em>{{ __('site.achievers.medals_local') }}</em>
          <span class="kc-bar"><span style="--w:{{ 100 - $intlShare }}%"></span></span></span>
          <span class="kc-go" aria-hidden="true">←</span>
        </button>
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

  @if ($profileItems)
    {{-- Design 9: tiles, timeline and reader are drawn by js/aj-bento.js from #aj-data (App\Support\Site\ProfileAchievements). --}}
    <section class="section wrap" id="achievements">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2>{{ __('site.achievers.achievements') }}</h2></div>
      <div class="aj" data-aj></div>
      @php $ajData = ['items' => $profileItems]; @endphp
      <script type="application/json" id="aj-data">{!! json_encode($ajData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
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

  @php
    $gallery = $achiever->getMedia('gallery');
    $photoItems = $gallery->map(function ($image) use ($achiever) {
        $caption = \App\Support\Localized::get($image->getCustomProperty('caption'));

        return [
            'full' => \App\Support\Media\Src::of($image),
            'thumb' => $image->hasGeneratedConversion('card') ? $image->getUrl('card') : \App\Support\Media\Src::of($image),
            'focal' => \App\Models\Achiever::focal($image),
            'caption' => $caption,
            'label' => $caption !== '' ? $caption : $achiever->name,
        ];
    })->values();
    $videoItems = $achievements->filter(fn ($achievement) => filled($achievement->video_url))->unique('video_url')->map(function ($video) use ($heroImage) {
        $youtube = preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([\w-]{11})~', $video->video_url, $m) ? $m[1] : null;

        return [
            'url' => $video->video_url,
            'youtube' => $youtube,
            // YouTube's wide picture (no black bars); the small wide one when a video has no HD picture.
            'thumb' => $youtube ? 'https://i.ytimg.com/vi/'.$youtube.'/hq720.jpg' : ($video->imageUrl('cover', 'card') ?? $heroImage),
            'fallback' => $youtube ? 'https://i.ytimg.com/vi/'.$youtube.'/mqdefault.jpg' : null,
            'title' => (string) $video->title,
            'year' => $video->achieved_at?->year,
        ];
    })->values();
    // Each library has a cover: a mosaic of its first pictures, or the achiever's photo while it is empty.
    $fallbackCover = $achiever->imageUrl('journey') ?? $achiever->imageUrl('portrait', 'card') ?? $heroImage;
    $coverOf = fn ($items) => $items->filter(fn ($item) => filled($item['thumb']))->take(3)
        ->map(fn ($item) => ['src' => $item['thumb'], 'fallback' => $item['fallback'] ?? null])->values()->all()
        ?: ($fallbackCover ? [['src' => $fallbackCover, 'fallback' => null]] : []);
    $covers = ['videos' => $coverOf($videoItems), 'photos' => $coverOf($photoItems)];
  @endphp
  {{-- «المكتبة المرئية» (تصاميم ٢٠٣٠): غلافان مصمَّمان — الصور مطبوعات مكدّسة من ألبوم المنجز على رمادي حجري، والفيديو سينمائي بصورة
       المنجز المفرّغة — ونافذة متوسطة: شبكة (٥ صور / ٣ مقاطع في الصف) وعارض بالتنقل و«رجوع» (js/sections/polish/achiever-library.js). --}}
  @php
      $mlPhotos = $photoItems->map(fn ($p) => ['img' => $p['thumb'], 'full' => $p['full'], 't' => $p['label'], 'focal' => $p['focal']])->values();
      $mlVideos = $videoItems->map(fn ($v) => ['id' => $v['youtube'], 'url' => $v['url'], 'img' => $v['thumb'], 'fb' => $v['fallback'], 't' => $v['title'],
          'y' => $v['year'] ? \App\Support\ArabicDate::digits($v['year']) : ''])->values();
      $mlFan = $mlPhotos->isEmpty() ? collect() : collect(range(0, 3))->map(fn ($i) => $mlPhotos[$i % $mlPhotos->count()]);
      $mlCut = $achiever->imageUrl('cutout') ?? $achiever->imageUrl('portrait');
      $mlName = $achiever->shortName();
  @endphp
  <section class="section wrap profile-library ml" data-ml aria-labelledby="library-title"
      data-media='@json(['name' => $achiever->name, 'photos' => $mlPhotos, 'videos' => $mlVideos])'>
    <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="library-title">{{ __('site.achievers.gallery') }}</h2></div>
    <div class="ml-covers">
      <button type="button" class="ml-cv ph" data-ml-open="photo" aria-haspopup="dialog">
        <span class="ml-fx cams" aria-hidden="true"></span>
        <span class="ml-fan" aria-hidden="true">
          @foreach ($mlFan->reverse()->values() as $k => $ph)
            @php $n = 3 - $k; @endphp
            <span class="ml-pr p{{ $n }}"><img src="{{ $ph['img'] }}" alt="" loading="lazy" style="object-position:{{ $ph['focal'] }}">@if ($n === 0)<i class="tape"></i><small>{{ $ph['t'] }}</small>@if ($mlPhotos->count() > 1)<b class="more">+{{ \App\Support\ArabicDate::digits($mlPhotos->count() - 1) }}</b>@endif @endif</span>
          @endforeach
        </span>
        <span class="ml-txt">
          <span class="kick"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 8.5A2.5 2.5 0 0 1 5.5 6H8l1.5-2h5L16 6h2.5A2.5 2.5 0 0 1 21 8.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5z"/><circle cx="12" cy="13" r="4"/></svg>{{ __('site.achievers.photo_library') }}</span>
          <span class="name">{{ $mlName }}</span>
          <span class="sub">{{ \App\Support\ArabicCount::of($mlPhotos->count(), 'photos') }}</span>
          <span class="open">{{ __('site.achievers.open_album') }} <span aria-hidden="true">←</span></span>
        </span>
      </button>
      <button type="button" class="ml-cv vd" data-ml-open="video" aria-haspopup="dialog">
        <span class="beam" aria-hidden="true"></span>
        <span class="ml-fx tris" aria-hidden="true"></span>
        @if ($mlCut)<img class="who-img" src="{{ $mlCut }}" alt="" loading="lazy">@endif
        <span class="playring" aria-hidden="true"></span>
        <span class="ml-txt">
          <span class="kick"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M10 8.5v7l6-3.5z" fill="currentColor"/></svg>{{ __('site.achievers.video_library') }}</span>
          <span class="name">{{ $mlName }}</span>
          <span class="sub">{{ \App\Support\ArabicCount::of($mlVideos->count(), 'videos') }}</span>
          <span class="open">{{ __('site.achievers.watch_now') }} <span aria-hidden="true">←</span></span>
        </span>
      </button>
    </div>
    <div class="ml-md" data-ml-md role="dialog" aria-modal="true" aria-labelledby="library-title" hidden>
      <div class="ml-card">
        <div class="ml-head"><span class="nm"><small>{{ __('site.achievers.gallery') }}</small><b>{{ $achiever->name }}</b></span><div class="ml-tabs" data-ml-tabs></div>
          <button type="button" class="ml-x" data-ml-x aria-label="{{ __('site.achievers.close') }}">×</button></div>
        <div class="ml-body" data-ml-body></div>
      </div>
    </div>
  </section>

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
