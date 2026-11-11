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
<section class="section wrap home-statistics" aria-label="{{ __('site.homepage.stats') }}">
  <div class="hs-gold" data-gold-stats>
    @foreach ($defaults as $i => $default)
      @php
          $n = (int) ($section->setting('stats.'.$i.'.value') ?: $auto[$i]);
          $label = \App\Support\Localized::get($section->setting('stats.'.$i.'.label')) ?: $default;
      @endphp
      <div class="hs-i" role="group" aria-label="{{ $label }}: {{ \App\Support\ArabicDate::digits($n) }}">{!! $icons[$i] !!}<span class="hs-num" data-n="{{ $n }}" aria-hidden="true">{{ \App\Support\ArabicDate::digits($n) }}</span><span class="hs-l">{{ $label }}</span></div>
    @endforeach
  </div>
</section>
