{{-- Design index.html .home-statistics (js/home-stats-gold.js). Numbers: the section settings, or counted from the site. --}}
@php
    $icons = ['<svg class="hs-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="3"/><path d="M6.5 20v-2.5a5.5 5.5 0 0 1 11 0V20M5.5 10a2.5 2.5 0 0 1 0-5m13 5a2.5 2.5 0 0 0 0-5"/></svg>', '<svg class="hs-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="5"/><path d="m8.5 13-1 8 4.5-2.5 4.5 2.5-1-8M10 9l1.4 1.4L14.5 7"/></svg>', '<svg class="hs-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.5"/><rect x="14" y="4" width="6" height="6" rx="1.5"/><rect x="4" y="14" width="6" height="6" rx="1.5"/><rect x="14" y="14" width="6" height="6" rx="1.5"/></svg>'];
    $auto = [
        \App\Models\Achiever::query()->published()->count(),
        \App\Models\Achievement::query()->published()->count(),
        \App\Models\Field::query()->count(),
    ];
    $defaults = [__('site.homepage.stat_achievers'), __('site.homepage.stat_achievements'), __('site.homepage.stat_fields')];
@endphp
@php
    $title = trim($section->text('title', __('site.homepage.stats_title')));
    $words = preg_split('/\s+/u', $title);
    $last = count($words) > 1 ? array_pop($words) : null;
    // Background: map of Oman (resources/site/assets/svg/icon-oman-map.svg) with its cities lighting up one after another.
    $land = 'M33.05 39.30 L33.27 35.66 L35.72 31.93 L35.75 28.24 L39.56 26.46 L38.07 25.20 L38.76 19.32 L43.07 19.29 L46.85 25.45 L51.55 28.73 L57.74 29.91 L62.73 31.56 L66.54 36.74 L68.81 39.73 L71.83 40.88 L71.81 42.89 L68.74 48.27 L67.39 50.81 L63.84 53.70 L60.70 59.88 L56.87 59.41 L55.12 61.56 L53.77 66.14 L54.80 72.18 L54.01 73.29 L50.13 73.26 L44.86 76.63 L44.04 81.03 L42.11 82.94 L36.87 82.86 L33.57 85.14 L33.61 88.79 L29.53 91.29 L24.88 90.44 L19.24 93.49 L15.35 94.00 L12.59 87.69 L6.00 72.79 L31.29 63.76 L36.91 45.70 L33.05 39.30Z M41.92 12.15 L40.32 9.07 L42.77 6.00 L43.82 6.78 L43.02 10.51 L41.92 12.15Z';
    // Khasab, Buraimi, Sohar, Muscat, Ibri, Nizwa, Ibra, Sur, Haima, Duqm, Thumrait, Salalah (map units).
    $cities = [[42.3, 9], [38.6, 28.5], [48, 27.6], [62, 33.6], [41.5, 36], [54.5, 37.5], [63.5, 40.5], [69, 41.5], [45, 60], [57, 57.5], [24.5, 82.5], [27, 88.5]];
@endphp
<section class="section wrap home-statistics" aria-labelledby="hs-title">
  <header class="hs-head">
    <span class="hs-kick">{{ $section->text('eyebrow', __('site.homepage.stats_eyebrow')) }}</span>
    <h2 id="hs-title">{{ implode(' ', $words) }}@if ($last) <em>{{ $last }}</em>@endif</h2>
    @if ($lead = $section->text('lead', __('site.homepage.stats_lead')))<p>{{ $lead }}</p>@endif
  </header>
  <div class="hs-gold" data-gold-stats>
    @foreach ($defaults as $i => $default)
      @php
          $n = (int) ($section->setting('stats.'.$i.'.value') ?: $auto[$i]);
          $label = \App\Support\Localized::get($section->setting('stats.'.$i.'.label')) ?: $default;
      @endphp
      <div class="hs-i" role="group" aria-label="{{ $label }}: {{ \App\Support\ArabicDate::digits($n) }}">{!! $icons[$i] !!}<span class="hs-num" data-n="{{ $n }}" aria-hidden="true">{{ \App\Support\ArabicDate::digits($n) }}</span><span class="hs-l">{{ $label }}</span></div>
    @endforeach
    <div class="hs-bg" aria-hidden="true">
      <img class="hs-logo" src="{{ Vite::asset('resources/site/assets/svg/logo.svg') }}" alt="">
      <svg class="hs-map" viewBox="0 0 77.83 100" focusable="false"><path class="hs-land" pathLength="1" d="{{ $land }}"/>@foreach ($cities as $i => [$x, $y])<g class="hs-city" style="--i:{{ $i }}"><circle class="glow" cx="{{ $x }}" cy="{{ $y }}" r="2.2"/><circle class="ping" cx="{{ $x }}" cy="{{ $y }}" r="1.1"/><circle class="dot" cx="{{ $x }}" cy="{{ $y }}" r="1.05"/></g>@endforeach</svg>
    </div>
  </div>
</section>
