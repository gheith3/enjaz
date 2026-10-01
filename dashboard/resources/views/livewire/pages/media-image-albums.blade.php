@section('title', __('site.media.albums_title'))

<div>
<x-inner-header :title="__('site.media.albums_title')" />

<main class="wash-block relative overflow-hidden">
  <section class="relative px-[4rem] lg:px-0 lg:mx-[14rem] pt-[10rem] pb-[14rem]">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[3rem]">
      @foreach ($albums as $album)
        <x-media-tile
          :title="$album->t('title')"
          :image="($album->images[0] ?? null) ? \Illuminate\Support\Facades\Storage::url($album->images[0]) : null"
          :url="route('media.images-albums.show', $album)"
        >
          <x-slot:icon>
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path stroke-linejoin="round" d="m4 18 5.5-5.5 3.5 3.5 2.5-2.5L20 18"/></svg>
          </x-slot:icon>
        </x-media-tile>
      @endforeach
    </div>
    {{ $albums->links('components.pagination') }}
  </section>
</main>
</div>
