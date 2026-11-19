@props(['organization'])
{{-- An organization as a compact card (css org-cards): its logo in a small box, its name and type, an arrow. --}}
<a class="oc s-{{ $organization->type?->cssKey() ?? 'gov' }}" href="{{ $organization->url() }}">
  <span class="oc-logo"><x-site.organization.emblem :organization="$organization" /></span>
  <span class="oc-t"><b>{{ $organization->name }}</b>@if ($organization->type)<small>{{ $organization->type->label }}</small>@endif</span>
  <svg class="oc-go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
</a>
