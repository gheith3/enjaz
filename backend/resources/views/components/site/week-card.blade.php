@props(['achiever', 'first' => false])
{{-- "منجز / منجزة الأسبوع" card (design achievers.html .week-card). --}}
@php $female = $achiever->isFemale(); @endphp
<a @class(['week-card', 'week-2' => ! $female]) href="{{ $achiever->url() }}">
  <span class="week-b">
    <span class="week-kick"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.achievers.week_'.($female ? 'f' : 'm')) }}</span>
    <span class="week-name" @if ($female) id="week-title" @endif role="heading" aria-level="2"><span>{{ $achiever->name }}</span></span>
    <span class="week-m">{{ collect([$achiever->field?->name, $achiever->headline])->filter()->implode(' · ') }}</span>
    @if ($achiever->week_teaser)<q>{{ $achiever->week_teaser }}</q>@endif
    <span class="week-cta">{{ __('site.achievers.week_cta_'.($female ? 'f' : 'm')) }} <span aria-hidden="true">←</span></span>
  </span>
  <x-site.oman-week-map :id="$first ? 'mapGold' : 'mapGold2'" />
  @if ($cutout = $achiever->imageUrl('cutout'))
    <img class="week-cut" src="{{ $cutout }}" alt="{{ $achiever->name }}" width="735" height="781">
  @endif
</a>
