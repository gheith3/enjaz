{{-- Design index.html .home-sports (js/home-sports.js): people picked in the section's items (image = transparent cutout,
     label = their sport). Each position keeps one of the design's four ribbon layouts; the first card is repeated at the
     end as in the design (phone loop). --}}
@php
    $layouts = ['ali', 'hamed', 'ghulam', 'mohammed'];
    $items = $section->items()->with(['media', 'itemable'])->get();
@endphp
@if ($items->isNotEmpty())
<section class="section wrap home-sports" aria-labelledby="sports-title">
  <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="sports-title">{{ $section->text('title', __('site.homepage.sports')) }}</h2></div>
  <div class="sports-grid">
    @foreach ($items->concat($items->take(1)) as $i => $item)
      @php
          $layout = $layouts[$i % count($layouts)];
          if ($i === $items->count()) { $layout = $layouts[0]; }
          $name = $item->displayTitle();
          $href = $item->href();
          $cut = $item->image('image');
      @endphp
      <a @class(['sports-person', 'sports-dup' => $i === $items->count()]) href="{{ $href ?? route('achievers.index') }}" data-cat="{{ $section->text('title', __('site.homepage.sports')) }}" data-name="{{ $name }}"
         @if ($item->isExternal()) target="_blank" rel="noopener noreferrer" aria-label="{{ $name }}، {{ $item->label }} — {{ __('site.homepage.external_page') }}" @endif>
        <div class="sports-photo sports-motion-{{ $layout }}">
          @include('site.sections.home.partials.sports-'.$layout)
          @if ($cut)<img class="sports-cutout" src="{{ $cut->getUrl() }}" alt="{{ $name }}" loading="lazy" decoding="async" width="1122" height="1402">@endif
        </div>
        <div class="sports-caption"><h3>{{ $name }}</h3>@if ($item->label)<span class="sports-field">{{ $item->label }}</span>@endif</div>
      </a>
    @endforeach
  </div>
  <div class="sports-more"><a class="btn-more" href="{{ $section->setting('more_url') ?: route('achievers.index') }}">{{ $section->text('more_label', __('site.homepage.discover_more')) }}<span class="sr-only"> {{ __('site.homepage.more_sports_sr') }}</span></a></div>
</section>
@endif
