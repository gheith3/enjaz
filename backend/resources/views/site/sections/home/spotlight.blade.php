{{-- Design index.html .aisha-feature (js/home-aisha.js): one person picked in the section's items (image = transparent portrait). --}}
@php
    $item = $section->items()->with(['media', 'itemable'])->first();
    $portrait = $item?->image('image');
@endphp
@if ($item)
<section class="section aisha-feature" aria-labelledby="aisha-title">
  <div class="aisha-art">
    @include('site.sections.home.partials.spotlight-back')
    @if ($portrait)<img class="aisha-portrait" src="{{ $portrait->getUrl() }}" alt="{{ \App\Models\PageSectionItem::alt($portrait, $item->displayTitle()) }}" loading="lazy" width="420" height="539">@endif
    @include('site.sections.home.partials.spotlight-front')
  </div>
  <div class="aisha-copy">
    <span class="aisha-label">{{ $item->label ?: $section->text('title', __('site.homepage.spotlight')) }}</span>
    <h2 id="aisha-title">{{ $item->displayTitle() }}</h2>
    <div class="aisha-bio">@if ($item->text)<p>{{ $item->text }}</p>@endif
      @if ($href = $item->href())<a href="{{ $href }}" @if ($item->isExternal()) target="_blank" rel="noopener noreferrer" @endif>{{ $item->link_label ?: __('site.homepage.read_about') }} <span aria-hidden="true">←</span></a>@endif</div>
  </div>
</section>
@endif
