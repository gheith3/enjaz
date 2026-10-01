@section('title', __('site.media.videos_title'))

<div>
<x-inner-header :title="__('site.media.videos_title')" />

<main class="wash-block relative overflow-hidden">
  <section class="relative px-[4rem] lg:px-0 lg:mx-[14rem] pt-[10rem] pb-[14rem]">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[3rem]">
      @foreach ($videos as $video)
        <x-media-tile
          :title="$video->t('title')"
          :image="$video->cover_image_path ? \Illuminate\Support\Facades\Storage::url($video->cover_image_path) : null"
          :url="$video->url"
          target="_blank"
          rel="noopener"
        >
          <x-slot:icon>
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path fill="#fff" stroke="none" d="M10 9.2v5.6l4.6-2.8L10 9.2Z"/></svg>
          </x-slot:icon>
        </x-media-tile>
      @endforeach
    </div>
    {{ $videos->links('components.pagination') }}
  </section>
</main>
</div>
