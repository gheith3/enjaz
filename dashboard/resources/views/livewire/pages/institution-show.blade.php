@section('title', __('site.institutions.title_with_name', ['name' => $organization->t('name')]))

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($organization->t('description'), 160) }}">
    <meta property="og:title" content="{{ __('site.institutions.title_with_name', ['name' => $organization->t('name')]) }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($organization->t('description'), 160) }}">
    <meta property="og:type" content="website">
@endsection

@php
    /** @var \App\Models\Organization $organization */
    $description = trim((string) $organization->t('description'));
    $paragraphs = $description === '' ? [] : preg_split('/\R{2,}/', $description);
    $managerMessage = trim((string) $organization->t('manager_message'));
    $managerName = trim((string) $organization->t('manager_name'));
    $hasManager = $managerMessage !== '' || $managerName !== '' || $organization->manager_image_path;
@endphp

<div>
  <!-- ===== Institution header (1920 x 650) ===== -->
  <header class="bleed relative lg:h-[65rem] pt-[18rem] pb-[14rem] lg:p-0 bg-[linear-gradient(210deg,#0c7f89_0%,#0e4348_100%)]">
    <div class="relative px-[4rem] lg:p-0 flex flex-col items-start gap-[3rem] lg:block">
      @if ($organization->logo_white_path || $organization->logo_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($organization->logo_white_path ?: $organization->logo_path) }}" alt="{{ $organization->t('name') }}" class="w-[20rem] lg:absolute lg:top-[22.5rem] lg:right-[20.4rem] lg:w-[26.1rem]">
      @endif
      <div class="text-white lg:absolute lg:top-[31.9rem] lg:right-[56.2rem] lg:whitespace-nowrap">
        @if ($organization->organizationType)
          <p class="m-0 text-[3.5rem] leading-[7rem]">{{ $organization->organizationType->t('title') }}</p>
        @endif
        <h1 class="m-0 -mt-[2rem] text-[5rem] leading-[10rem] font-normal">{{ $organization->t('name') }}</h1>
      </div>
    </div>
  </header>

  <main class="wash-block relative">
    <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[44rem] top-[66rem] w-[100rem] brightness-0 opacity-[0.03] pointer-events-none">

    <!-- About card (overlaps the header) -->
    @if ($paragraphs)
      <section class="relative z-10 mx-[4rem] lg:mx-0 lg:mr-[14rem] lg:w-[164rem] -mt-[9.8rem] min-h-[47.8rem] bg-white rounded-[4.9rem] shadow-[0_0.3rem_0.6rem_rgba(124,124,124,0.16)] pt-[11.4rem] pb-[6rem] px-[4rem] lg:pr-[8.8rem] lg:pl-0">
        <div class="article-body lg:w-[145.9rem] text-[2.6rem] leading-[3.6rem] text-[#171717]">
          @foreach ($paragraphs as $paragraph)
            <p>{!! nl2br(e($paragraph)) !!}</p>
          @endforeach
        </div>
      </section>
    @endif

    <!-- Manager quote -->
    @if ($hasManager)
      <section class="relative px-[4rem] lg:px-0 mt-[9rem] lg:mt-0 lg:h-[69.6rem] flex flex-col-reverse lg:block gap-[4rem]">
        <div class="lg:absolute lg:top-[29.8rem] lg:right-[84.4rem] lg:w-[81.4rem]">
          @if ($managerMessage !== '')
            <p class="m-0 text-[3.5rem] leading-[5rem] text-[#0d0d0d] lg:whitespace-nowrap">{!! nl2br(e($managerMessage)) !!}</p>
          @endif
          @if ($managerName !== '')
            <p class="m-0 mt-[5.9rem] text-[4.6rem] leading-[5.1rem] font-bold text-[#096770]">{{ $managerName }}</p>
          @endif
        </div>
        @if ($organization->manager_image_path)
          <div class="relative mx-auto lg:mx-0 w-[59.2rem] max-w-full h-[51rem] lg:absolute lg:top-[9rem] lg:right-[20.4rem]">
            <span class="absolute bottom-0 inset-x-0 h-[36.7rem] rounded-[4.6rem] border border-[#707070] bg-[linear-gradient(180deg,#0c7f89_0%,#0e4348_100%)]"></span>
            <img src="{{ \Illuminate\Support\Facades\Storage::url($organization->manager_image_path) }}" alt="{{ $managerName }}" class="absolute bottom-0 right-[2.2rem] w-[52.9rem] max-w-[96%] max-h-[51rem] object-cover object-top">
          </div>
        @endif
      </section>
    @endif

    <!-- Our achievements (news carousel) -->
    @if ($items->isNotEmpty())
      <section class="relative px-[4rem] lg:px-0 mt-[10rem] lg:mt-0 pb-[22.4rem]" x-data="carousel(@js($items->count()))">
        <x-section-title :title="__('site.institutions.our_achievements')" class="lg:mr-[18rem] lg:pt-[1.2rem] !gap-[2.1rem]" />
        <div class="relative mt-[7.4rem] lg:mt-[7.4rem]">
          <div class="overflow-hidden lg:mr-[14.1rem]">
            <div class="max-lg:!transform-none transition-transform duration-500" :style="'transform: translateX(' + (-offset) + '%)'">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-[3.8rem] lg:flex lg:gap-0 lg:w-[calc(var(--n)*25%)]" style="--n: {{ $items->count() }}">
                @foreach ($items as $item)
                  <div class="lg:flex-none lg:w-[calc(100%/var(--n))] lg:px-[1.9rem]">
                    <x-news-card :item="$item" class="lg:w-full" />
                  </div>
                @endforeach
              </div>
            </div>
          </div>
          @if ($items->count() > 4)
            <button type="button" @click="next()" class="hidden lg:flex absolute right-[5.6rem] top-[18.7rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white items-center justify-center border-0 cursor-pointer hover:brightness-110" aria-label="{{ __('site.institutions.next') }}">
              <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/></svg>
            </button>
            <button type="button" @click="prev()" class="hidden lg:flex absolute left-[5.5rem] top-[18.7rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white items-center justify-center border-0 cursor-pointer hover:brightness-110" aria-label="{{ __('site.institutions.previous') }}">
              <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 6-6 6 6 6"/></svg>
            </button>
          @endif
        </div>
      </section>
    @endif

    <!-- More institutions band -->
    @if ($more->isNotEmpty())
      <section class="bleed relative bg-[#f7f7f7] lg:h-[53.4rem] px-[4rem] lg:px-0 pt-[5.2rem] pb-[6rem] lg:pb-0">
        <h2 class="m-0 lg:mr-[14rem] text-[3.2rem] leading-[4.4rem] font-bold text-black">{{ __('site.more') }}</h2>
        <div class="mt-[3.2rem] grid grid-cols-2 lg:grid-cols-[repeat(4,35.3rem)] gap-[3rem] lg:gap-x-[5.8rem] lg:mr-[16.4rem]">
          @foreach ($more as $other)
            <x-org-card :organization="$other" />
          @endforeach
        </div>
      </section>
    @endif
  </main>
</div>
