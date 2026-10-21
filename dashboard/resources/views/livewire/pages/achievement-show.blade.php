@php
    /** @var \App\Models\Achievement $achievement */
    $title = $achievement->t('title');
    $excerpt = $achievement->t('excerpt') ?: \Illuminate\Support\Str::limit($achievement->t('description'), 160);
    $coverUrl = $achievement->cover_image_path ? \Illuminate\Support\Facades\Storage::url($achievement->cover_image_path) : null;
@endphp

@section('title', $title . ' | ' . __('site.meta.site_name'))

@section('meta')
    <meta name="description" content="{{ $excerpt }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $excerpt }}">
    <meta property="og:type" content="article">
    @if ($coverUrl)
        <meta property="og:image" content="{{ $coverUrl }}">
    @endif
@endsection

<div>
  <!-- ===== Article header (1920 x 854) ===== -->
  <header class="bleed relative z-10 lg:h-[85.4rem] pt-[18rem] pb-[6rem] lg:p-0 bg-[#3d6563] bg-cover bg-center" @if ($coverUrl) style="background-image:url('{{ $coverUrl }}')" @endif>
    <div class="relative px-[4rem] lg:p-0">
      <img src="{{ asset('images/brand/mark.svg') }}" alt="" aria-hidden="true" class="lg:absolute lg:top-[57.5rem] lg:right-[68.4rem] w-[12.5rem] h-[7.8rem]">
      <h1 class="lg:absolute lg:top-[68.1rem] lg:right-[67.9rem] lg:max-w-[124rem] mt-[2rem] lg:mt-0 mb-0 text-white text-[4rem] leading-[4.8rem] font-bold">{{ $title }}</h1>
    </div>

    @if ($achiever)
      @php($tileImage = $achiever->tile_image_path ?: $achiever->image_path)
      <a href="{{ route('achievers.show', $achiever) }}" class="tile !block mx-auto mt-[5rem] lg:m-0 lg:!absolute lg:top-[43.9rem] lg:right-[23.3rem] z-10">
        @if ($tileImage)
          <img src="{{ \Illuminate\Support\Facades\Storage::url($tileImage) }}" alt="{{ $achiever->t('name') }}">
        @endif
        <span class="absolute top-[34.3rem] right-[6.8rem] text-white text-[4rem] leading-[4.8rem] font-bold whitespace-nowrap">{{ $achiever->t('name') }}</span>
      </a>
    @endif
  </header>

  <main class="wash-block relative">
    <!-- Article body -->
    <article class="relative mx-[4rem] lg:mx-0 lg:mr-[14rem] lg:w-[164rem] mt-[7.5rem] bg-white rounded-[4.9rem] shadow-[0_0.3rem_0.6rem_rgba(124,124,124,0.16)] pt-[6.3rem] pb-[8rem] px-[4rem] lg:pr-[19.2rem] lg:pl-[8.2rem]">
      <div class="flex items-start justify-between gap-[3rem]">
        <ul class="list-none m-0 p-0 flex flex-wrap gap-[3.6rem]">
          @if ($achievement->achieved_at)
            <li class="h-[4.6rem] w-[16.6rem] rounded-[2.3rem] bg-[#a88344] text-white text-[3.2rem] leading-[4.6rem] font-bold text-center">{{ $achievement->achieved_at->format('Y') }}</li>
          @endif
          @if ($achievement->achievementMainType)
            <li class="h-[4.6rem] w-[26.2rem] rounded-[2.3rem] bg-[#a88344] text-white text-[3.2rem] leading-[4.6rem] font-bold text-center">{{ $achievement->achievementMainType->t('title') }}</li>
          @endif
          @if ($achievement->t('country'))
            <li class="h-[4.6rem] w-[24rem] rounded-[2.3rem] bg-[#a88344] text-white text-[3.2rem] leading-[4.6rem] font-bold text-center">{{ $achievement->t('country') }}</li>
          @endif
        </ul>
        <button type="button" class="shrink-0 bg-transparent border-0 p-0 cursor-pointer" aria-label="{{ __('site.achievements.share') }}"
                onclick="navigator.share ? navigator.share({title: document.title, url: location.href}) : navigator.clipboard.writeText(location.href)">
          <svg class="w-[3.5rem] h-[4.6rem]" viewBox="0 0 35 46" fill="none" stroke="#505050" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 17H4.5a2 2 0 0 0-2 2v22.5a2 2 0 0 0 2 2h26a2 2 0 0 0 2-2V19a2 2 0 0 0-2-2H23"/><path d="M17.5 30V3M10 10l7.5-7.5L25 10"/></svg>
        </button>
      </div>

      <div class="article-body mt-[7rem] lg:w-[123.6rem] lg:mr-[1rem] text-[2.6rem] leading-[3.6rem] text-[#171717]">
        {!! nl2br(e($achievement->t('description'))) !!}
      </div>
    </article>

    <!-- More achievements band -->
    @if ($related->count())
      <section class="bleed relative mt-[15.7rem] bg-[#f7f7f7] lg:h-[99rem] px-[4rem] lg:px-0 pt-[6.8rem] pb-[6rem] lg:pb-0">
        <h2 class="m-0 lg:mr-[14rem] text-[3.2rem] leading-[4.4rem] font-bold text-black">{{ __('site.more') }}</h2>
        <div class="mt-[8rem] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[repeat(3,50.9rem)] gap-[5.7rem] lg:mr-[17.3rem]">
          @foreach ($related as $item)
            <x-achievement-card :achievement="$item" />
          @endforeach
        </div>
      </section>
    @endif
  </main>
</div>
