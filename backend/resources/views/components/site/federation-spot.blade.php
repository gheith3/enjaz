{{-- Design: «شريط الاتحاد المختار» above the results (css federations .fed-spot). --}}
@props(['federation', 'countLabel', 'field' => null, 'unit' => 'achievements'])
<div class="fed-spot" wire:key="fed-spot-{{ $federation['slug'] }}">
  <span class="spot-logo"><img src="{{ $federation['logo'] ?: Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="{{ __('site.federations.logo', ['name' => $federation['name']]) }}"></span>
  <span class="spot-txt">
    <span class="spot-eyebrow">{{ __('site.federations.eyebrow_'.$unit) }}</span>
    <span class="spot-name">{{ $federation['name'] }}</span>
    <span class="spot-count">{{ $countLabel }}@if ($field) {{ __('site.federations.in', ['field' => $field]) }}@endif</span>
    <a class="spot-link" href="{{ $federation['url'] }}">{{ __('site.federations.page') }} <span aria-hidden="true">←</span></a>
  </span>
  <button type="button" class="spot-close" aria-label="{{ __('site.federations.clear') }}" wire:click="setOrg('')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
  </button>
</div>
