@props(['organization'])
{{-- Organization card of the design's institutions.html list. --}}
<a class="org-card" href="{{ $organization->url() }}">
  <span class="org-logo">@if ($logo = $organization->logoUrl())<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
  <span class="org-info"><b>{{ $organization->name }}</b><small class="org-type {{ $organization->type?->css_class }}">{{ $organization->type?->label }}</small></span>
  <span class="org-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span>
</a>
