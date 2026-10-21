@section('title', __('site.media.title'))

<div>
<x-inner-header :title="__('site.media.heading')" :search-placeholder="__('site.media.search_placeholder')" />

<main class="wash-block relative">
  <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[34.6rem] top-[0.8rem] w-[77rem] brightness-0 opacity-[0.035] pointer-events-none">

  <!-- Latest news -->
  <section class="relative px-[4rem] lg:px-0 pt-[10rem] pb-[14rem]">
    <x-section-title :title="__('site.media.latest_news')" class="lg:mr-[18rem] !gap-[2.1rem]" />
    <div class="mt-[7.4rem] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[repeat(4,38.1rem)] gap-[3.8rem] lg:gap-y-[5.7rem] lg:mr-[14.1rem]">
      @foreach ($news as $item)
        <x-news-card :item="$item" />
      @endforeach
    </div>
    {{ $news->links('components.pagination') }}
  </section>

  <!-- Media library (same component as the About page) -->
  <x-media-library />
</main>
</div>
