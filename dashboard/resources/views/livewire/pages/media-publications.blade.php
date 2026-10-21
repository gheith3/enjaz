@section('title', __('site.media.publications_title'))

<div>
<x-inner-header :title="__('site.media.publications_title')" />

<main class="wash-block relative">
  <section class="relative px-[4rem] lg:px-0 lg:mx-[14rem] pt-[10rem] pb-[14rem]">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[3rem]">
      @foreach ($publications as $publication)
        <x-media-tile
          :title="$publication->t('title')"
          :image="$publication->cover_image_path ? \Illuminate\Support\Facades\Storage::url($publication->cover_image_path) : null"
          :url="\Illuminate\Support\Facades\Storage::url($publication->file_path)"
          download
        >
          <x-slot:icon>
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path stroke-linejoin="round" d="M6 3.5h11a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1-1.5Z"/><path d="M5 18.5A1.5 1.5 0 0 1 6.5 17H18"/></svg>
          </x-slot:icon>
        </x-media-tile>
      @endforeach
    </div>
    {{ $publications->links('components.pagination') }}
  </section>
</main>
</div>
