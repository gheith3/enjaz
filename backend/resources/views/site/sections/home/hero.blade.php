{{-- Design index.html .hero-majlis (js/home-hero-majlis.js): the leaders' words, one slide each (dashboard: كلمات القيادة). --}}
@php $slides = \App\Models\LeaderQuote::query()->active()->with('media')->get(); @endphp
@if ($slides->isNotEmpty())
<section class="home-hero hero-cinema hero-majlis" data-majlis aria-roledescription="{{ __('site.homepage.slider') }}" aria-label="{{ $section->text('title', __('site.homepage.leaders')) }}">
<div class="hm-slides">
  @foreach ($slides as $i => $q)
    @php $portrait = $q->image('portrait'); @endphp
    <div @class(['hm-slide', 'hm-slide--'.$q->variant, 'is-active' => $i === 0]) role="group" aria-roledescription="{{ __('site.homepage.slide') }}" aria-label="{{ __('site.homepage.words_of', ['name' => $q->fullName()]) }}">
    <div class="grid"><p class="hd-by" aria-hidden="true"><span class="hd-t">{{ $q->honorific }}</span><span class="hd-nm">{{ $q->name }}</span><span class="hd-ps">{{ $q->postnominal }}</span></p><i class="hd-vline" aria-hidden="true"></i><i class="hd-hline" aria-hidden="true"></i><i class="hd-star" aria-hidden="true"></i><i class="hd-qmark" aria-hidden="true">”</i>
      <div class="quote">
        <blockquote><span class="hc-lead">{{ $q->lead }}</span> <span class="hc-body">{{ $q->body }}</span></blockquote>
        <p class="by"><b>{{ $q->fullName() }}</b>@if ($q->postnominal)<span>{{ $q->postnominal }}</span>@endif</p>
      </div>
      <div class="hc-pic">@if ($q->variant === 'sultan')<img class="hc-logo" src="{{ Vite::asset('resources/site/assets/svg/logo.svg') }}" alt="" aria-hidden="true" width="117" height="94">@include('site.sections.home.partials.hero-rays')@endif
        @if ($portrait)<img class="portrait" src="{{ $portrait->getUrl() }}" @if ($i === 0) data-slide-img fetchpriority="high" @else decoding="async" @endif alt="{{ \App\Models\LeaderQuote::alt($portrait, $q->fullName()) }}" @if ($w = $portrait->getCustomProperty('width')) width="{{ $w }}" height="{{ $portrait->getCustomProperty('height') }}" @endif>@endif</div>
    </div>
    </div>
  @endforeach
</div>
@include('site.sections.home.partials.hero-arc')
</section>
<div class="home-hero-cue"></div>
@endif
