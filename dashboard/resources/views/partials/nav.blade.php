@php
    /** @var \App\Settings\GeneralSettings $settings */
    $settings = app(\App\Settings\GeneralSettings::class);
    $logoUrl = $settings->logo ? \Illuminate\Support\Facades\Storage::url($settings->logo) : asset('images/brand/logo-white.png');
    $siteName = app()->getLocale() === 'ar'
        ? ($settings->name_ar ?: $settings->name_en)
        : ($settings->name_en ?: $settings->name_ar);
    $siteName = $siteName ?: __('site.meta.site_name');

    $navLinks = [
        ['route' => 'about', 'patterns' => ['about'], 'label' => __('site.nav.about')],
        ['route' => 'achievements.index', 'patterns' => ['achievements.*'], 'label' => __('site.nav.achievements')],
        ['route' => 'achievers.index', 'patterns' => ['achievers.*'], 'label' => __('site.nav.achievers')],
        ['route' => 'institutions.index', 'patterns' => ['institutions.*'], 'label' => __('site.nav.institutions')],
        ['route' => 'media', 'patterns' => ['media', 'media.*'], 'label' => __('site.nav.media')],
        ['route' => 'contact', 'patterns' => ['contact'], 'label' => __('site.nav.contact')],
    ];
@endphp

<div x-data="siteNav">
  <!-- Logo -->
  <a href="{{ route('home') }}" class="absolute z-20 top-[4rem] right-[4rem] lg:top-[6.2rem] lg:right-[19.8rem]" aria-label="{{ $siteName }}">
    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="w-[12rem] lg:w-[11.7rem]">
  </a>

  <!-- Desktop nav -->
  <nav class="site-nav hidden lg:flex absolute z-20 top-[7.5rem] right-[96.8rem] gap-[5.05rem]">
    @foreach ($navLinks as $link)
      <a href="{{ route($link['route']) }}" @class(['is-active' => request()->routeIs(...$link['patterns'])])>{{ $link['label'] }}</a>
    @endforeach
  </nav>

  <!-- Mobile nav -->
  <button @click="mobileOpen = !mobileOpen" class="lg:hidden absolute z-30 top-[5rem] left-[4rem] text-white p-2" aria-label="{{ __('site.nav.menu') }}">
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
  </button>
  <div x-show="mobileOpen" x-cloak x-transition class="lg:hidden absolute z-30 inset-x-[4rem] top-[18rem] rounded-[3rem] bg-[#063135]/95 p-[3rem]">
    <nav class="site-nav flex flex-col gap-[3rem] text-right">
      @foreach ($navLinks as $link)
        <a href="{{ route($link['route']) }}" @class(['!text-[3rem]', '!text-[#91c46c]' => request()->routeIs(...$link['patterns'])])>{{ $link['label'] }}</a>
      @endforeach
    </nav>
  </div>
</div>
