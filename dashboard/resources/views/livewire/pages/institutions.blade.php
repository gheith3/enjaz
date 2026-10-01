@section('title', __('site.institutions.meta_title'))

<div>
  <x-inner-header :title="__('site.institutions.title')" />

  <!-- ===== Filters + logo grid ===== -->
  <main class="relative px-[4rem] lg:px-0 pb-[8.9rem]">
    <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[34.6rem] top-[0.8rem] w-[77rem] brightness-0 opacity-[0.035] pointer-events-none">

    <div class="relative pt-[7.7rem] flex flex-wrap gap-[2rem] lg:gap-[3.2rem] lg:mr-[16.5rem]">
      <x-filter-pills :items="$pills" :active="$type" param="type" />
    </div>

    <div class="relative mt-[5.2rem] grid grid-cols-2 lg:grid-cols-[repeat(4,35.3rem)] gap-[3rem] lg:gap-x-[5.8rem] lg:gap-y-[6rem] lg:mr-[16.4rem]">
      @forelse ($organizations as $organization)
        <x-org-card :organization="$organization" />
      @empty
        <p class="col-span-full m-0 text-center text-[2.6rem] leading-[3.6rem] text-[#1b1e24]">{{ __('site.institutions.no_results') }}</p>
      @endforelse
    </div>

    {{ $organizations->links('components.pagination') }}
  </main>
</div>
