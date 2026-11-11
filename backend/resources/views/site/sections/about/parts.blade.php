{{-- about.html sections 3–9 and «استكشف». Content comes from the page's sections (texts) and their items; the page script
     renders the lists from window.INJAZ_ABOUT. A section turned off in the dashboard is kept hidden (the script needs it). --}}
@php
    $sec = $page->sections->keyBy('key');
    $off = fn (string $key) => isset($sec[$key]) && ! $sec[$key]->is_enabled;
    $items = fn (string $key) => isset($sec[$key]) ? $sec[$key]->items()->with('media')->get() : collect();
    $founderItem = $items('about.founder')->first();
    $img = fn ($it, $c = 'image') => $it->getFirstMediaUrl($c) ?: '';
    $ach = $items('about.achievements');
    $anchors = ['about.fields' => '#fields', 'about.journey' => '#journey', 'about.founder' => '#founder', 'about.testimonials' => '#testimonials',
        'about.team' => '#team', 'about.achievements' => '#our-achievements', 'about.media' => '#media'];
    $nav = collect([['#top', $page->label('nav_top', __('site.about.nav_top'))], ['#mission', $page->label('nav_mission', __('site.about.nav_mission'))]])
        ->concat(collect($anchors)->filter(fn ($a, $key) => ! $off($key))
            ->map(fn ($a, $key) => [$a, $sec[$key]?->text('nav') ?: __('site.about.nav_'.\Illuminate\Support\Str::after($key, 'about.'))])->values());
    $data = [
        'nav' => $nav->values()->all(),
        'fields' => $items('about.fields')->map(fn ($it) => ['t' => $it->displayTitle(), 'd' => (string) $it->text, 'img' => $img($it), 'ico' => $img($it, 'icon')])->all(),
        'founder' => array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', (string) $founderItem?->text)))),
        'quotes' => $items('about.testimonials')->map(fn ($it) => ['t' => $it->flag('highlight') ? 'official' : 'person', 'q' => (string) $it->text, 'n' => $it->displayTitle(), 'r' => (string) $it->label, 'img' => $img($it)])->all(),
        'team' => $items('about.team')->map(fn ($it) => ['n' => $it->displayTitle(), 'r' => (string) $it->label, 'img' => $img($it)])->all(),
        'achCats' => $ach->pluck('label')->filter()->unique()->values()->prepend(__('site.about.all'))->all(),
        'achievements' => $ach->map(fn ($it) => ['t' => $it->displayTitle(), 'c' => (string) $it->label, 'img' => $img($it)])->all(),
        'media' => $items('about.media')->map(fn ($it) => ['t' => $it->displayTitle(), 'ico' => $img($it, 'icon'), 'img' => $img($it), 'url' => $it->href()])->all(),
        'roadmap' => $items('about.journey')->map(fn ($it) => ['y' => \App\Support\Localized::get($it->settings['meta'] ?? null), 'date' => (string) $it->label,
            's' => $it->displayTitle(), 't' => (string) ($it->text ?: $it->displayTitle()), 'img' => $img($it), 'logo' => $it->flag('highlight')])->all(),
    ];
@endphp
<script>window.INJAZ_ABOUT = {!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>

<section @if ($off('about.fields')) hidden @endif class="fields" id="fields" aria-labelledby="fields-title">
  <div class="intro">
    <span class="eyebrow" style="color:var(--gold-2)">{{ $sec['about.fields']?->text('eyebrow') }}</span>
    <h2 id="fields-title">{!! $em($sec['about.fields']?->text('title')) !!}</h2>
    <p>{{ $sec['about.fields']?->text('lead') }}</p>
  </div>
  <div class="stage" data-stage aria-roledescription="معرض دوّار" aria-label="مجالات الإنجاز">
    <div class="reflection" aria-hidden="true"></div>
    <div class="carousel" data-carousel></div>
  </div>
  <div class="stage-ctrl">
    <button class="round" type="button" aria-label="المجال السابق" data-ring-prev><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg></button>
    <button class="round" type="button" aria-label="إيقاف الدوران التلقائي" aria-pressed="false" data-ring-pause><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg></button>
    <button class="round" type="button" aria-label="المجال التالي" data-ring-next><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg></button>
  </div>
</section>

<section @if ($off('about.journey')) hidden @endif class="sec wrap journey roadmap" id="journey" aria-labelledby="journey-title" style="padding-top:10px" data-roadmap data-img="">
  <div class="sec-head reveal"><span class="mark"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""></span><h2 id="journey-title">{{ $sec['about.journey']?->text('title', 'خارطة العمل') }}</h2><span class="line"></span></div>
  <div class="rm-tl" data-rm-tl>
    <span class="rm-stars" aria-hidden="true" data-rm-stars></span>
    <svg class="rm-svg" aria-hidden="true"><defs><linearGradient id="rm-g1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF1D2"/><stop offset="1" stop-color="#C9A35C"/></linearGradient></defs><path class="rm-bg"/><path class="rm-fg"/></svg>
    <span class="rm-spark" data-rm-spark></span>
    <ol></ol>
  </div>
  <p class="journey-note">{{ $sec['about.journey']?->text('lead', __('site.about.journey_note')) }}</p>
  <dialog class="rm-sheet" data-rm-sheet aria-label="تفاصيل المحطة">
    <button class="rm-x" type="button" aria-label="إغلاق" data-rm-x><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
    <div class="rm-media" data-rm-media></div>
    <div class="rm-body"><div class="rm-row"><span class="rm-n" data-rm-n></span><span class="rm-tag" data-rm-date></span></div><p class="rm-full" data-rm-t></p>
      <div class="rm-nav"><button type="button" data-rm-prev>→ السابقة</button><button type="button" data-rm-next>التالية ←</button></div></div>
  </dialog>
</section>

<section @if ($off('about.founder')) hidden @endif class="wrap reveal" id="founder" aria-labelledby="founder-title" style="padding-block:10px 44px">
  <article class="founder" data-founder>
    <div class="fd-photo ct-dk" aria-hidden="true">@if ($card = $founderItem?->getFirstMediaUrl('image2'))<img src="{{ $card }}" alt="" loading="lazy">@endif<b>{{ $founderItem?->displayTitle() }}</b><small>{{ $founderItem?->label }}</small></div>
    <div class="founder-top">@if ($ph = $founderItem?->getFirstMediaUrl('image'))<img src="{{ $ph }}" alt="{{ $founderItem->displayTitle() }}">@endif<div><b id="founder-title">{{ $founderItem?->displayTitle() }}</b><small>{{ $founderItem?->label }}</small></div></div>
    <div class="founder-body"><div class="q" aria-hidden="true">“</div><h3>{{ \App\Support\Localized::get($founderItem?->settings['meta'] ?? null) }}</h3><div class="founder-text" id="founder-text" data-founder-text></div></div>
    <button class="founder-toggle" type="button" aria-expanded="false" aria-controls="founder-text" data-founder-toggle><span>{{ __('site.about.read_word') }}</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></button>
  </article>
</section>

<section @if ($off('about.testimonials')) hidden @endif class="sec wrap reveal" id="testimonials" aria-labelledby="t-title" style="padding-top:0">
  <div class="sec-head"><span class="mark"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""></span><h2 id="t-title">{{ $sec['about.testimonials']?->text('title', 'قالوا عن إنجازات') }}</h2><span class="line"></span></div>
  <div class="tm" data-tm aria-roledescription="شهادات">
    <div class="tm-dome">
      <span class="tm-stars" aria-hidden="true" data-tm-stars></span>
      <svg class="tm-arc" viewBox="0 0 320 140" preserveAspectRatio="none" aria-hidden="true"><path d="M14 136 Q160 -24 306 136" fill="none" stroke="#E9CF96" stroke-opacity=".55" stroke-dasharray="2 6"/></svg>
      <span class="tm-halo" aria-hidden="true"></span>
      <svg class="tm-qarc" viewBox="-120 0 240 264" aria-hidden="true"><path d="M-84 214 Q0 262 84 214" fill="none" stroke="#E9CF96" stroke-opacity=".5" stroke-dasharray="2 5"/></svg>
      <div class="tm-orbit" data-tm-orbit></div>
    </div>
    <div class="tm-body" aria-live="polite" data-tm-body></div>
    <div class="tm-nav">
      <button class="round" type="button" aria-label="الشهادة السابقة" data-tm-prev><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg></button>
      <span class="tm-count" data-tm-count></span>
      <button class="round" type="button" aria-label="الشهادة التالية" data-tm-next><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg></button>
    </div>
  </div>
</section>

<section @if ($off('about.team')) hidden @endif class="sec wrap reveal" id="team" aria-labelledby="team-title" style="padding-top:0">
  <div class="sec-head"><span class="mark"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""></span><h2 id="team-title">{{ $sec['about.team']?->text('title', 'فريق العمل') }}</h2><span class="line"></span></div>
  <div class="tdeck" data-team>
    <div class="tdeck-cards" data-cards></div>
    <div class="tdeck-bar" aria-hidden="true"><i data-bar></i></div>
  </div>
</section>

<section @if ($off('about.achievements')) hidden @endif class="sec wrap reveal" id="our-achievements" aria-labelledby="a-title" style="padding-top:0">
  <div class="sec-head"><span class="mark"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""></span><h2 id="a-title">{{ $sec['about.achievements']?->text('title', 'إنجازاتنا') }}</h2><span class="line"></span></div>
  <div class="filters" role="group" aria-label="تصفية الإنجازات" data-filters></div>
  <div class="ach-row" data-ach-row></div>
  <div class="row-ctrl"><button class="round" type="button" aria-label="الإنجاز السابق" data-row-prev><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg></button><div class="row-bar" aria-hidden="true"><i data-row-bar></i></div><button class="round" type="button" aria-label="الإنجاز التالي" data-row-next><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg></button></div>
</section>

<section @if ($off('about.media')) hidden @endif class="sec wrap reveal" id="media" aria-labelledby="m-title" style="padding-top:0">
  <div class="sec-head"><span class="mark"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""></span><h2 id="m-title">{{ $sec['about.media']?->text('title', 'المكتبة الإعلامية') }}</h2><span class="line"></span></div>
  <div class="media" data-media></div>
</section>

<section class="explore" aria-label="{{ __('site.about.explore') }}">
  <div class="field-info" aria-live="off" data-field-info></div>
  <div class="explore-dots" aria-hidden="true" data-explore-dots></div>
</section>
