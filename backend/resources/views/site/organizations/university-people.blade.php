{{-- Design: uni-people.html (filters and cards in livewire/site/organization-people). --}}
<x-site.layout :title="__('site.university.people_of', ['name' => $organization->name])" styles="uni-people" body-class="flush"
    :noindex="! $organization->isLive()" class="u-page" data-up-page
    :crumb="['title' => $listPage->title, 'url' => $organization->listUrl(), 'sub' => $organization->name]">
  <section class="u-hero up-hero"><div class="uh-top"><div class="uh-txt"><span class="uh-kick">{{ __('site.university.people_kicker') }}</span>
    <h1>{{ __('site.university.people_of', ['name' => $organization->name]) }}</h1><p class="uh-about">{{ __('site.university.people_about') }}</p></div>
    <a @class(['uh-emb', 'is-plate' => $organization->hasWideLogo()]) href="{{ $organization->url() }}">@if ($logo = $organization->logoUrl())<img src="{{ $logo }}" alt="{{ $organization->name }}">@endif</a></div></section>
  <livewire:site.organization-people :organization="$organization" />
</x-site.layout>
