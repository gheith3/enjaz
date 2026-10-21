@php
    /** @var \App\Models\PublicFigure $figure */
    $name = $figure->t('name');
    $headline = $figure->t('headline');
    $portrait = $figure->cutout_image_path ?: $figure->image_path;
    $story = $figure->t('story');
    $description = $figure->t('description');

    $handle = fn (?string $url) => '@'.ltrim(\Illuminate\Support\Str::afterLast(rtrim((string) $url, '/'), '/'), '@');

    $socials = collect([
        ['url' => $figure->linkedin_url, 'icon' => 'linkedin-teal.svg', 'alt' => __('site.achievers.social.linkedin'), 'label' => $figure->linkedin_url ? $handle($figure->linkedin_url) : null, 'icon_w' => 'w-[3.3rem]', 'gap' => 'gap-[1.8rem]'],
        ['url' => $figure->email ? 'mailto:'.$figure->email : null, 'icon' => 'email-teal.svg', 'alt' => __('site.achievers.social.email'), 'label' => $figure->email, 'icon_w' => 'w-[3.6rem]', 'gap' => 'gap-[2.1rem]'],
        ['url' => $figure->instagram_url, 'icon' => 'instagram-teal.svg', 'alt' => __('site.achievers.social.instagram'), 'label' => $figure->instagram_url ? $handle($figure->instagram_url) : null, 'icon_w' => 'w-[2.9rem]', 'gap' => 'gap-[2rem]'],
        ['url' => $figure->twitter_url, 'icon' => 'twitter-teal.svg', 'alt' => __('site.achievers.social.twitter'), 'label' => $figure->twitter_url ? $handle($figure->twitter_url) : null, 'icon_w' => 'w-[3.9rem]', 'gap' => 'gap-[1.8rem]'],
    ])->filter(fn ($social) => filled($social['url']));

    $mediaWidths = ['lg:w-[57.1rem]', 'lg:w-[48.5rem] lg:mr-[2.9rem]', 'lg:w-[52.4rem] lg:mr-[3.1rem]'];
@endphp

@section('title', __('site.achievers.title_with_name', ['name' => $name]))

@section('meta')
<meta name="description" content="{{ $headline ?: \Illuminate\Support\Str::limit(strip_tags((string) ($description ?: '')), 160) }}">
<meta property="og:title" content="{{ $name }} | {{ __('site.meta.site_name') }}">
<meta property="og:description" content="{{ $headline }}">
<meta property="og:type" content="profile">
@if ($portrait)
<meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::url($portrait) }}">
@endif
@endsection

<div>
<!-- ===== Profile header (1920 x 621) ===== -->
<header class="bleed relative lg:h-[62.1rem] pt-[18rem] lg:p-0 bg-[linear-gradient(211deg,#0c7f89_0%,#0e4348_100%)]">
  <div class="relative z-10 px-[4rem] lg:p-0 text-white">
    <h1 class="lg:absolute lg:top-[28.6rem] lg:right-[69.2rem] m-0 text-[5.2rem] leading-[5.4rem] font-bold lg:whitespace-nowrap">{{ $name }}</h1>
    @if ($figure->name_en)
      <p class="lg:absolute lg:top-[36.2rem] lg:right-[69.2rem] mt-[1.5rem] lg:mt-0 mb-0 text-[3.7rem] leading-[5.4rem] whitespace-nowrap" dir="ltr">{{ $figure->name_en }}</p>
    @endif
    @if ($headline)
      <div class="relative mt-[3rem] lg:mt-0 lg:absolute lg:top-[46.2rem] lg:right-[69.2rem] lg:w-[94.9rem] min-h-[7.8rem] flex items-center pr-[6.6rem] pl-[3rem] py-[1.5rem] lg:py-0">
        <span class="absolute inset-0 rounded-[3.9rem] opacity-[0.17] shadow-[0_0.3rem_0.6rem_rgba(0,0,0,0.16)] bg-[linear-gradient(259deg,#0c7f89_0%,#0e4348_100%)]"></span>
        <span class="relative text-[2.4rem] leading-[3.4rem] font-bold">{{ $headline }}</span>
      </div>
    @endif
  </div>

  @if ($portrait)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($portrait) }}"
         alt="{{ $name }}" class="relative block mt-[4rem] lg:mt-0 lg:absolute lg:left-[118.3rem] lg:top-[14.4rem] w-full lg:w-[84.8rem] lg:h-[47.7rem] lg:object-cover lg:object-top max-w-none
                lg:[mask-image:linear-gradient(to_right,transparent_0,#000_12rem)]">
  @endif
</header>

<main class="wash-block relative">
  <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[46rem] top-[60rem] w-[100rem] brightness-0 opacity-[0.03] pointer-events-none">

  <!-- Social links -->
  @if ($socials->isNotEmpty())
    <ul class="relative list-none m-0 px-[4rem] lg:px-0 pt-[6.5rem] lg:pt-[6.7rem] flex flex-wrap gap-y-[2rem] gap-x-[3.4rem] lg:mr-[69.2rem] text-[2.2rem] leading-[3.2rem] text-[#5a5a5a]">
      @foreach ($socials as $social)
        <li><a href="{{ $social['url'] }}" class="flex items-center {{ $social['gap'] }} text-inherit no-underline hover:text-[#096770]"><img src="{{ asset('images/icons/'.$social['icon']) }}" alt="{{ $social['alt'] }}" class="{{ $social['icon_w'] }}"><span dir="ltr">{{ $social['label'] }}</span></a></li>
      @endforeach
    </ul>
  @endif

  <div class="relative px-[4rem] lg:px-0">
    <!-- Story -->
    @if ($story || $description)
      <section x-data="readMore()" class="mt-[9.8rem] lg:mr-[14rem] lg:w-[164rem] min-h-[39.4rem] bg-white rounded-[4.9rem] shadow-[0_0.3rem_0.6rem_rgba(124,124,124,0.16)] pt-[5.1rem] pb-[4.4rem] pr-[4rem] lg:pr-[11.7rem] pl-[4rem] lg:pl-[16.6rem] relative">
        <h2 class="m-0 text-[4.6rem] leading-[6.4rem] font-bold text-[#096770]">{{ __('site.achievers.story') }}</h2>
        @if ($story)
          <div class="mt-[1.4rem] mb-0 text-[2.6rem] leading-[3.6rem] text-[#171717]" :class="open ? '' : 'line-clamp-4'">{!! $story !!}</div>
        @else
          <p class="mt-[1.4rem] mb-0 text-[2.6rem] leading-[3.6rem] text-[#171717]" :class="open ? '' : 'line-clamp-4'">{{ $description }}</p>
        @endif
        <button type="button" @click="open = !open" class="mt-[2.4rem] lg:mt-0 lg:absolute lg:left-[13.1rem] lg:bottom-[5.3rem] flex items-center gap-[1.9rem] bg-transparent border-0 p-0 cursor-pointer text-[2.2rem] leading-[2.5rem] text-[#5a5a5a] font-[inherit]">
          <span x-text="open ? @js(__('site.achievers.read_less')) : @js(__('site.read_more'))"></span>
          <svg class="w-[1.6rem] h-[0.9rem] transition-transform" :class="open && 'rotate-180'" viewBox="0 0 16 9" fill="none" stroke="#5a5a5a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l7 7 7-7"/></svg>
        </button>
      </section>
    @endif

    <!-- Local / international counts -->
    <section class="mt-[5rem] lg:mr-[14rem] lg:w-[164rem] lg:h-[20.9rem] bg-white rounded-[4.9rem] shadow-[0_0.3rem_0.6rem_rgba(124,124,124,0.16)] relative flex justify-center gap-[8rem] py-[4rem] lg:p-0 lg:block text-center">
      <div class="lg:absolute lg:top-[4.9rem] lg:right-[51.5rem] lg:w-[30rem]">
        <p class="m-0 text-[4.6rem] leading-[6.4rem] font-bold text-[#096770]">{{ $internationalCount }}</p>
        <p class="m-0 text-[3.3rem] leading-[4.7rem] text-[#171717]">{{ __('site.achievers.international_achievements') }}</p>
      </div>
      <div class="lg:absolute lg:top-[4.9rem] lg:right-[82.4rem] lg:w-[30rem]">
        <p class="m-0 text-[4.6rem] leading-[6.4rem] font-bold text-[#096770]">{{ $localCount }}</p>
        <p class="m-0 text-[3.3rem] leading-[4.7rem] text-[#171717]">{{ __('site.achievers.local_achievements') }}</p>
      </div>
    </section>

    <!-- Visual library -->
    @if ($mediaItems->isNotEmpty())
      <h2 class="mt-[9rem] mb-0 lg:mr-[14rem] text-[4.6rem] leading-[6.4rem] font-bold text-[#096770]">{{ __('site.achievers.visual_library') }}</h2>
      <div class="mt-[6.2rem] grid grid-cols-1 sm:grid-cols-3 gap-[3rem] lg:flex lg:gap-0 lg:mr-[14rem] lg:h-[34.5rem]">
        @foreach ($mediaItems as $index => $item)
          <x-media-tile :title="$item['title']" :image="\Illuminate\Support\Facades\Storage::url($item['image'])" :url="$item['url']" class="{{ $mediaWidths[$index] ?? '' }}">
            <x-slot:icon>
              @if ($item['kind'] === 'video')
                <svg viewBox="0 0 24 24" fill="#fff"><path d="M8 5.5v13l10.5-6.5L8 5.5Z"/></svg>
              @else
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path stroke-linejoin="round" d="m4 18 5.5-5.5 3.5 3.5 2.5-2.5L20 18"/></svg>
              @endif
            </x-slot:icon>
          </x-media-tile>
        @endforeach
      </div>
    @endif

    <!-- My achievements -->
    <section>
      <h2 class="mt-[10.2rem] mb-0 lg:mr-[14rem] text-[4.6rem] leading-[6.4rem] font-bold text-[#096770]">{{ __('site.achievers.my_achievements') }}</h2>
      <div class="relative mt-[6.7rem] flex flex-wrap gap-[2rem] lg:gap-[2.7rem] lg:mr-[14rem]">
        <x-filter-pills :items="$scopeTabs" :active="$scope" param="scope" />
        <button type="button" wire:click="toggleSort" title="{{ $sort === 'desc' ? __('site.achievers.sort_newest') : __('site.achievers.sort_oldest') }}" class="filter-pill gap-[2.8rem] lg:absolute lg:top-0 lg:left-[16rem]">
          <span class="flex flex-col gap-[0.5rem]"><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i></span>
          <span>{{ __('site.achievers.sort') }}</span>
        </button>
      </div>

      <div class="mt-[8rem] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[repeat(3,50.9rem)] gap-[5.7rem] lg:mr-[13.9rem]">
        @forelse ($achievements as $achievement)
          <x-achievement-card :achievement="$achievement" />
        @empty
          <p class="col-span-full text-[2.6rem] leading-[3.6rem] text-[#5a5a5a]">{{ __('site.achievements.no_results') }}</p>
        @endforelse
      </div>

      {{ $achievements->links() }}
    </section>
  </div>

  <!-- More achievers band -->
  @if ($related->isNotEmpty())
    <section class="bleed relative mt-[24.3rem] bg-[#f7f7f7] lg:h-[53.4rem] px-[4rem] lg:px-0 pt-[3.1rem] pb-[6rem] lg:pb-0">
      <h2 class="m-0 lg:mr-[14rem] text-[3.2rem] leading-[4.4rem] font-bold text-black">{{ __('site.more') }}</h2>
      <div class="mt-[4.6rem] grid grid-cols-2 sm:grid-cols-3 lg:flex gap-[3rem] lg:gap-[4.9rem] lg:mr-[19.6rem]">
        @foreach ($related as $relatedFigure)
          <x-person-tile :figure="$relatedFigure" size="sm" />
        @endforeach
      </div>
    </section>
  @endif
</main>
</div>
