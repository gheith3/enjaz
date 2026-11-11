{{-- Design index.html .home-achievers (js/main.js strip): achievers marked «شريط الرئيسية», in their order. --}}
@php
    $people = \App\Models\Achiever::query()->published()->where('show_in_home_strip', true)->with('media')->orderBy('sort_order')->orderBy('id')->get();
@endphp
@if ($people->isNotEmpty())
<section class="section wrap home-achievers" aria-label="{{ $section->text('title', __('site.homepage.achievers')) }}" data-strip>
  <div class="strip-frame">
    <button class="strip-arrow" type="button" data-strip-prev aria-label="{{ __('site.homepage.prev_achiever') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button>
    <div class="strip" aria-label="{{ __('site.homepage.achievers_photos') }}">
    @foreach ($people as $i => $a)
      @php $photo = $a->image('portrait') ?? $a->image('avatar'); @endphp
      <button type="button" data-name="{{ $a->name }}" data-href="{{ $a->url() }}" aria-label="{{ __('site.homepage.open_page', ['name' => $a->name]) }}" @if ($i === 2) class="center" @endif @if ($i > 4) hidden @endif><span class="strip-photo">@if ($photo)<img src="{{ $a->imageUrl($photo->collection_name, 'card') }}" alt="" style="object-position:{{ $photo->getCustomProperty('focal') ?: 'center top' }}">@endif</span><span class="strip-name">{{ $a->name }}</span></button>
    @endforeach
    </div>
    <button class="strip-arrow" type="button" data-strip-next aria-label="{{ __('site.homepage.next_achiever') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg></button>
  </div>
  <div class="strip-nav">
    <a class="strip-discover" href="{{ route('achievers.index') }}">{{ $section->text('more_label', __('site.homepage.more_achievers')) }} <span aria-hidden="true">←</span></a>
  </div>
</section>
@endif
