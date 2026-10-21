@section('title', __('site.achievers.meta_title'))

<div>
<x-inner-header :title="__('site.achievers.title')" :with-illustration="true" />

<!-- ===== Filters + grid ===== -->
<main class="relative px-[4rem] lg:px-0 pb-[8rem]">
  <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[34.6rem] top-[0.8rem] w-[77rem] brightness-0 opacity-[0.035] pointer-events-none">
  <div class="relative pt-[7.7rem]">
    <div class="flex flex-wrap gap-[2rem] lg:grid lg:grid-cols-[repeat(6,16.8rem)] lg:gap-x-[2.7rem] lg:gap-y-[1.3rem] lg:mr-[62rem] lg:w-[114.3rem]">
      <x-filter-pills :items="$pills" :active="$type" param="type" />
    </div>
    <button type="button" class="filter-pill mt-[2rem] lg:mt-0 lg:absolute lg:top-[17.3rem] lg:right-[15.5rem] gap-[2.8rem]">
      <span class="flex flex-col gap-[0.5rem]"><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i></span>
      <span>{{ __('site.filter') }}</span>
    </button>
  </div>

  <div class="mt-[5.9rem] grid grid-cols-2 lg:grid-cols-[repeat(4,35.1rem)] gap-[3rem] lg:gap-x-[7rem] lg:gap-y-[7.1rem] lg:mr-[15.2rem]">
    @forelse ($figures as $figure)
      <x-person-tile :figure="$figure" size="lg" />
    @empty
      <p class="col-span-full text-[2.6rem] leading-[3.6rem] text-[#5a5a5a]">{{ __('site.achievers.no_results') }}</p>
    @endforelse
  </div>

  {{ $figures->links() }}
</main>
</div>
