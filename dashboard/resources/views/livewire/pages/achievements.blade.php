@section('title', __('site.achievements.meta_title'))

<div>
  <x-inner-header :title="__('site.achievements.title')" />

  <!-- ===== Filters + cards ===== -->
  <main class="relative px-[4rem] lg:px-0 pb-[11.5rem]">
    <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[34.6rem] top-[0.8rem] w-[77rem] brightness-0 opacity-[0.035] pointer-events-none">
    <div class="relative pt-[7.7rem] flex flex-wrap gap-[2rem] lg:block">
      <div class="flex flex-wrap gap-[2rem] lg:grid lg:grid-cols-[repeat(6,16.8rem)] lg:gap-x-[2.75rem] lg:mr-[62.8rem] lg:w-[114.55rem]">
        <x-filter-pills :items="$pillItems" :active="$type" param="type" />
      </div>
      <button type="button" class="filter-pill lg:absolute lg:top-[7.7rem] lg:right-[15.5rem] gap-[2.8rem]">
        <span class="flex flex-col gap-[0.5rem]"><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i><i class="block w-[2.2rem] h-[0.3rem] rounded bg-[#5c5c5c]"></i></span>
        <span>{{ __('site.filter') }}</span>
      </button>
    </div>

    @if ($achievements->count())
      <div class="mt-[5.9rem] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[repeat(3,50.9rem)] gap-[5.7rem] lg:mr-[13.9rem]">
        @foreach ($achievements as $achievement)
          <x-achievement-card :achievement="$achievement" />
        @endforeach
      </div>

      {{ $achievements->links('components.pagination') }}
    @else
      <p class="mt-[5.9rem] lg:mr-[13.9rem] m-0 text-[2.6rem] leading-[3.6rem] text-[#7a797e]">{{ __('site.achievements.no_results') }}</p>
    @endif
  </main>
</div>
