@section('title', $imageMedia->t('title'))

<div>
<x-inner-header :title="$imageMedia->t('title')" :show-search="false" />

<main class="wash-block relative">
  <section class="relative px-[4rem] lg:px-0 lg:mx-[14rem] pt-[10rem] pb-[14rem]">
    @if ($imageMedia->t('description'))
      <p class="m-0 mb-[5rem] text-[2.6rem] leading-[3.6rem] text-[#171717]">{{ $imageMedia->t('description') }}</p>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[3rem]">
      @foreach ($imageMedia->images ?? [] as $image)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" alt="{{ $imageMedia->t('title') }}" class="w-full h-[34.5rem] object-cover rounded-[3.2rem] shadow-[0_0.3rem_0.6rem_rgba(0,0,0,0.16)]">
      @endforeach
    </div>
  </section>
</main>
</div>
