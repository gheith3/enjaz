{{-- Design: uni-achievements.html (hero and back link here, filters and grid in livewire/site/organization-achievements). --}}
<x-site.layout :title="__('site.university.achievements_of', ['name' => $organization->name])" styles="uni-achievements" body-class="flush"
    :noindex="! $organization->isLive()" class="u-page" data-ua-page
    :crumb="['title' => $listPage->title, 'url' => $organization->listUrl(), 'sub' => $organization->name]">
  <livewire:site.organization-achievements :organization="$organization" />
</x-site.layout>
