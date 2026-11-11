{{-- البحث (no design page): Livewire SearchPage with the design's cards and rows. Not indexed. --}}
<x-site.layout :title="__('site.search_page.title')" styles="search" :noindex="true"
    :crumb="['title' => __('site.search_page.title'), 'url' => null, 'sub' => null]">
  <livewire:site.search-page />
</x-site.layout>
