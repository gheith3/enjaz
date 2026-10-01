@props([
    'title',
    'searchPlaceholder' => null,
    'showSearch' => true,
])

@php
    $searchPlaceholder = $searchPlaceholder ?? __('site.search.placeholder');
@endphp

<!-- ===== Inner header (1920 x 421) ===== -->
<header class="inner-header relative overflow-hidden lg:h-[42.1rem] pt-[18rem] pb-[6rem] lg:p-0">
  <img src="{{ asset('images/brand/mountain-flag.svg') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[96rem] top-[4.2rem] w-[116.3rem] max-w-none pointer-events-none">

  <h1 class="relative lg:absolute lg:top-[17.4rem] inset-x-0 m-0 text-center text-white text-[6.7rem] leading-[9.4rem] font-normal">{{ $title }}</h1>
  @if ($showSearch)
    <form class="search-box relative mx-auto mt-[3rem] lg:mt-0 lg:absolute lg:top-[28.9rem] lg:right-[64.6rem] max-lg:w-[calc(100%-8rem)]" role="search" action="{{ url()->current() }}" method="GET">
      <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $searchPlaceholder }}" aria-label="{{ $searchPlaceholder }}">
      <img src="{{ asset('images/icons/search.svg') }}" alt="">
    </form>
  @endif
</header>
