@props(['organization', 'counts' => null])
{{-- University / college card (design js/universities.js card()). --}}
@php
    $mono = mb_substr($organization->abbr ?: preg_replace('/[^A-Z]/', '', (string) $organization->getTranslation('name', 'en', false)), 0, 4);
    $links = collect([
        ($counts['own'] ?? 0) ? trans_choice('site.organizations.own_count', $counts['own'], ['n' => \App\Support\ArabicDate::digits($counts['own'])]) : null,
        ($counts['people'] ?? 0) ? trans_choice('site.organizations.people_count', $counts['people'], ['n' => \App\Support\ArabicDate::digits($counts['people'])]) : null,
        ($counts['members'] ?? 0) ? trans_choice('site.organizations.members_count', $counts['members'], ['n' => \App\Support\ArabicDate::digits($counts['members'])]) : null,
    ])->filter()->implode(' · ');
@endphp
<a class="u-card" href="{{ $organization->url() }}">
  <span class="u-logo" data-r="{{ $organization->logo_ratio ?: 1 }}">@if ($logo = $organization->logoUrl())<img src="{{ $logo }}" alt="" loading="lazy">@else<span class="u-mono" aria-hidden="true">{{ $mono }}</span>@endif</span>
  <span class="u-info"><b>{{ $organization->name }}</b><small>{{ collect([$organization->city, \App\Models\Organization::SECTORS[$organization->sector] ?? null])->filter()->implode(' · ') }}</small></span>
  @if ($links)<span class="u-links">{{ $links }}</span>@endif
</a>
