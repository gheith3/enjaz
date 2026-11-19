@props(['tech'])
{{-- University of Technology and Applied Sciences: the wide card with its branches (design js/universities.js). --}}
<a class="u-tech" href="{{ $tech->url() }}">
  <span class="u-tech-top"><span class="u-logo" data-r="{{ $tech->logo_ratio ?: 1 }}">@if ($logo = $tech->logoUrl())<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
    <span class="u-info">@if ($tech->formerly)<small>{{ __('site.organizations.formerly', ['name' => $tech->formerly]) }}</small>@endif<b>{{ $tech->name }}</b></span></span>
  <span class="u-branches">@foreach ($tech->units as $branch)<span>{{ $branch->name }}</span>@endforeach</span>
</a>
