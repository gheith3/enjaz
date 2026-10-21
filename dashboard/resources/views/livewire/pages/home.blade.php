@section('title', __('site.home.meta_title'))

@section('meta')
<meta name="description" content="{{ $metaDescription }}">
<meta property="og:title" content="{{ __('site.home.meta_title') }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
@if ($heroImageUrl)
<meta property="og:image" content="{{ $heroImageUrl }}">
@endif
@endsection

<div>
<!-- ===== Hero (1920 x 1080) ===== -->
<header class="hero-bg bleed relative lg:h-[108rem]">
  <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="watermark w-[110rem] top-[12rem] -right-[14rem]">

  <!-- Portrait (photo, ring and swoosh are one image in the design) -->
  @if ($heroImageUrl)
    <img src="{{ $heroImageUrl }}" alt="{{ $quoteAuthor }}"
         class="relative lg:absolute lg:left-0 lg:top-[15.2rem] w-full lg:w-[101.6rem] mt-[44rem] lg:mt-0
                [mask-image:linear-gradient(to_left,transparent_0,#000_10rem),linear-gradient(to_bottom,transparent_0,#000_5rem)]
                [mask-composite:intersect] [-webkit-mask-composite:source-in]">
  @endif

  <!-- Quote -->
  <div class="absolute top-[22rem] inset-x-[4rem] lg:inset-x-auto lg:top-[37.9rem] lg:right-[22.2rem] lg:w-[73.8rem] text-right z-10">
    @if ($quote !== '')
      <p class="text-white text-[3.3rem] leading-[4.7rem] lg:mr-0 lg:ml-auto lg:w-[65.5rem] m-0">{{ $quote }}</p>
    @endif
    @if ($quoteAuthor !== '')
      <div class="mt-[3.6rem] lg:mt-0 lg:absolute lg:top-[27.7rem] lg:right-0 w-full lg:w-[73.8rem] h-[8rem] rounded-[4rem] flex items-center pr-[2.9rem]
                  bg-[linear-gradient(270deg,rgba(5,57,61,0.48)_0%,rgba(6,49,53,0)_100%)]">
        <span class="text-[2.9rem] leading-[4.1rem] text-[#f4dba8]">{{ $quoteAuthor }}</span>
      </div>
    @endif
  </div>
</header>

<!-- ===== Carousel + stats ===== -->
<section class="bleed relative bg-[linear-gradient(180deg,#c0d8db_0rem,#ddeaeb_16rem,#f3f6f2_42rem,#ffffff_60rem)]">
  <div class="absolute inset-0 wash opacity-70 [mask-image:linear-gradient(to_bottom,transparent_0,#000_40rem)]"></div>

  <!-- Photo carousel -->
  @if ($carouselItems->isNotEmpty())
    <div class="relative pt-[3.3rem]" x-data="photoCarousel(@js($carouselItems))">
      <div dir="ltr" class="relative h-[39.7rem] flex items-center justify-center gap-[2.9rem]">
        <template x-for="(p, slot) in visible" :key="p.src">
          <a :href="p.url" class="block overflow-hidden transition-all duration-300 shrink-0"
             :class="slot === 2 ? 'w-[28.8rem] h-[39.7rem] rounded-[5rem] shadow-[0_0.6rem_1.6rem_rgba(0,0,0,0.18)]' : 'w-[23rem] h-[31.5rem] rounded-[4.2rem] max-lg:hidden'">
            <img :src="p.src" :alt="p.alt" class="w-full h-full object-cover">
          </a>
        </template>
        <button @click="prev()" class="absolute left-[19.7rem] max-lg:left-[3rem] top-[14.1rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white flex items-center justify-center hover:brightness-110" aria-label="{{ __('site.home.previous') }}">
          <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 6-6 6 6 6"/></svg>
        </button>
        <button @click="next()" class="absolute right-[19.7rem] max-lg:right-[3rem] top-[14.1rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white flex items-center justify-center hover:brightness-110" aria-label="{{ __('site.home.next') }}">
          <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/></svg>
        </button>
      </div>
    </div>
  @endif

  <!-- Stats card -->
  <div class="relative mt-[8.5rem] mx-[4rem] lg:ml-0 lg:mr-[19rem] lg:w-[148.9rem] lg:h-[39.9rem] bg-white rounded-[4.9rem] shadow-[0_0.3rem_0.6rem_rgba(124,124,124,0.16)]">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-[5rem] py-[6rem] lg:py-0 lg:pt-[6.5rem] lg:w-[115.5rem] lg:mr-[16.35rem] text-center">
      <div>
        <div class="h-[9.6rem] flex items-center justify-center"><img src="{{ asset('images/icons/stat-achievers.svg') }}" alt="" class="w-[9.6rem]"></div>
        <p class="mt-[3rem] text-[4.4rem] leading-[6.2rem] text-[#171717]">{{ __('site.home.stats_achievers') }}</p>
        <p class="text-[7.8rem] leading-[9.3rem] font-bold text-[#096770]">{{ $stats['achievers'] }}</p>
      </div>
      <div>
        <div class="h-[9.6rem] flex items-center justify-center"><img src="{{ asset('images/icons/stat-achievements.svg') }}" alt="" class="w-[7.4rem]"></div>
        <p class="mt-[3rem] text-[4.4rem] leading-[6.2rem] text-[#171717]">{{ __('site.home.stats_achievements') }}</p>
        <p class="text-[7.8rem] leading-[9.3rem] font-bold text-[#096770]">{{ $stats['achievements'] }}</p>
      </div>
      <div>
        <div class="h-[9.6rem] flex items-center justify-center"><img src="{{ asset('images/icons/stat-fields.svg') }}" alt="" class="w-[7.7rem]"></div>
        <p class="mt-[3rem] text-[4.4rem] leading-[6.2rem] text-[#171717]">{{ __('site.home.stats_fields') }}</p>
        <p class="text-[7.8rem] leading-[9.3rem] font-bold text-[#096770]">{{ $stats['fields'] }}</p>
      </div>
    </div>
  </div>

  <!-- ===== Latest achievements ===== -->
  <div class="relative pt-[16rem] pb-[26.4rem] px-[4rem] lg:px-0">
    <x-section-title :title="__('site.home.latest_achievements')" class="lg:mr-[15.3rem]" />

    @if ($latestAchievements->isNotEmpty())
      <div class="mt-[10rem] grid grid-cols-1 md:grid-cols-2 lg:flex lg:justify-start gap-[5.7rem] lg:mr-[13.9rem]">
        @foreach ($latestAchievements as $achievement)
          <x-achievement-card :achievement="$achievement" />
        @endforeach
      </div>

      <div class="mt-[7rem] lg:ml-[14.9rem] text-left"><a href="{{ route('achievements.index') }}" class="btn-more">{{ __('site.more') }}</a></div>
    @else
      <p class="mt-[10rem] lg:mr-[13.9rem] text-[2.6rem] leading-[3.6rem] text-[#7A797E]">{{ __('site.home.no_achievements') }}</p>
    @endif
  </div>
</section>

<!-- ===== Featured achiever ===== -->
@if ($featuredFigure)
  <section class="bleed relative lg:h-[83.4rem] bg-[#0c5a62] bg-cover bg-center"
           @if ($featuredBannerUrl) style="background-image: url('{{ $featuredBannerUrl }}');" @endif>
    <div class="relative px-[4rem] py-[8rem] lg:p-0 lg:absolute lg:inset-0">
      <h2 class="lg:absolute lg:top-[8.2rem] lg:right-[87.7rem] m-0 text-white text-[5.2rem] leading-[5.4rem] font-bold">{{ $featuredFigure->t('name') }}</h2>
      <div class="mt-[4rem] lg:mt-0 lg:absolute lg:top-[17.9rem] lg:right-[87.7rem] lg:w-[90.3rem] lg:h-[56.9rem] rounded-[3.3rem] bg-white/[0.77] pr-[5rem] pl-[5rem] lg:pl-0 pt-[6.5rem] pb-[5rem] lg:pb-0">
        <p class="m-0 lg:w-[68.8rem] text-[2.6rem] leading-[5.4rem] text-black">
          {{ $featuredExcerpt }}
          <a href="{{ route('achievers.show', $featuredFigure) }}" class="text-black hover:text-[#096770]">{{ __('site.read_more') }}</a>
        </p>
      </div>
    </div>
  </section>
@endif

<!-- ===== Category sections ===== -->
@if ($categoryRows->isNotEmpty())
  <section class="relative">
    @foreach ($categoryRows as $i => $type)
      <div class="wash-block relative px-[4rem] lg:px-0 {{ $i === 0 ? 'pt-[20.3rem]' : 'pt-[2.85rem]' }} {{ $loop->last ? 'pb-[32.2rem]' : '' }}">
        <x-section-title :title="$type->t('title')" class="lg:mr-[15.3rem]" />
        <div class="mt-[8.85rem] grid grid-cols-2 lg:flex gap-[3rem] lg:gap-[6.9rem] lg:mr-[15.6rem]">
          @foreach ($type->publicFigures as $figure)
            <x-person-tile :figure="$figure" />
          @endforeach
        </div>
        <div class="mt-[6.7rem] lg:ml-[16rem] text-left"><a href="{{ route('achievers.index', ['type' => $type->getKey()]) }}" class="btn-more">{{ __('site.more') }}</a></div>
      </div>
    @endforeach
  </section>
@endif
</div>
