@props(['organization', 'alt' => '', 'lazy' => true])
{{-- An organization's emblem in its arch («بوابة»): the logo, or for an organization without one a crest —
     a shield carrying the Omani eight-pointed star (css .emb-*, in orgs-diwan and org-profile). --}}
@php $logo = $organization->image('logo'); @endphp
@if ($logo)
  <img @class(['emb-logo', 'is-wide' => $organization->hasWideLogo()]) src="{{ $logo->getUrl() }}" alt="{{ $alt }}" @if ($lazy) loading="lazy" @endif decoding="async">
@else
  <svg class="emb-crest" viewBox="0 0 80 92" role="img" aria-label="{{ $alt ?: $organization->name }}" focusable="false">
    <path class="emb-c-out" d="M40 4C52 9 63 11 74 11V42C74 64 60 80 40 88 20 80 6 64 6 42V11C17 11 28 9 40 4Z"/>
    <path class="emb-c-in" d="M40 11C50 15 59 17 67 17V42C67 60 56 73 40 80 24 73 13 60 13 42V17C21 17 30 15 40 11Z"/>
    <g class="emb-c-star"><rect x="28" y="32" width="24" height="24" rx="1.5"/><rect x="28" y="32" width="24" height="24" rx="1.5" transform="rotate(45 40 44)"/></g>
    <circle class="emb-c-dot" cx="40" cy="44" r="4.5"/>
  </svg>
@endif
