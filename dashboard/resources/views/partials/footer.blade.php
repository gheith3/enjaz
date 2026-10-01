@php
    /** @var \App\Settings\GeneralSettings $settings */
    $settings = app(\App\Settings\GeneralSettings::class);
    $logoUrl = $settings->logo ? \Illuminate\Support\Facades\Storage::url($settings->logo) : asset('images/brand/logo-white.png');
    $siteName = app()->getLocale() === 'ar'
        ? ($settings->name_ar ?: $settings->name_en)
        : ($settings->name_en ?: $settings->name_ar);
    $siteName = $siteName ?: __('site.meta.site_name');
    $address = app()->getLocale() === 'ar'
        ? ($settings->address_ar ?: $settings->address_en)
        : ($settings->address_en ?: $settings->address_ar);

    $socialIcons = [
        'instagram' => 'images/icons/instagram.svg',
        'twitter' => 'images/icons/twitter.svg',
        'linkedin' => 'images/icons/linkedin.svg',
        'facebook' => 'images/icons/facebook.svg',
    ];
@endphp

<footer class="site-footer relative lg:h-[24.1rem] px-[4rem] py-[6rem] lg:p-0">
  <a href="{{ route('home') }}" class="block w-[13rem] lg:absolute lg:top-[6rem] lg:right-[14rem]"><img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="w-full"></a>
  <span class="hidden lg:block absolute top-[5.2rem] right-[34.2rem] w-px h-[14rem] bg-white/30"></span>
  <ul class="mt-[4rem] lg:mt-0 lg:absolute lg:top-[5.2rem] lg:right-[44.4rem] flex flex-col gap-[1.7rem] text-[1.6rem] leading-[2.2rem] tracking-[0.126rem] list-none p-0 m-0">
    <li><a href="{{ route('home') }}">{{ __('site.nav.home') }}</a></li>
    <li><a href="{{ route('about') }}">{{ __('site.nav.about') }}</a></li>
    <li><a href="{{ route('achievements.index') }}">{{ __('site.nav.achievements') }}</a></li>
    <li><a href="{{ route('achievers.index') }}">{{ __('site.nav.achievers') }}</a></li>
  </ul>
  <ul class="mt-[3rem] lg:mt-0 lg:absolute lg:top-[5.2rem] lg:right-[62.3rem] flex flex-col gap-[1.7rem] text-[1.6rem] leading-[2.2rem] tracking-[0.126rem] list-none p-0 m-0">
    <li><a href="{{ route('institutions.index') }}">{{ __('site.nav.institutions') }}</a></li>
    <li><a href="{{ route('media') }}">{{ __('site.nav.media') }}</a></li>
    <li><a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a></li>
  </ul>
  <span class="hidden lg:block absolute top-[5.2rem] right-[84rem] w-px h-[14rem] bg-white/30"></span>
  <ul class="mt-[3rem] lg:mt-0 lg:absolute lg:top-[5.5rem] lg:right-[94.3rem] flex flex-col gap-[1.9rem] text-[1.4rem] leading-[2rem] tracking-[0.111rem] list-none p-0 m-0">
    @if ($settings->phone)
      <li class="flex items-center gap-[2.2rem]"><img src="{{ asset('images/icons/phone.svg') }}" alt="" class="w-[1.9rem]"><a href="tel:{{ $settings->phone }}" dir="ltr">{{ $settings->phone }}</a></li>
    @endif
    @if ($settings->phone_secondary)
      <li class="flex items-center gap-[2.2rem]"><img src="{{ asset('images/icons/phone.svg') }}" alt="" class="w-[1.9rem]"><a href="tel:{{ $settings->phone_secondary }}" dir="ltr">{{ $settings->phone_secondary }}</a></li>
    @endif
    @if ($settings->email)
      <li class="flex items-center gap-[2.2rem]"><img src="{{ asset('images/icons/email.svg') }}" alt="" class="w-[1.9rem]"><a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></li>
    @endif
    @if ($address)
      <li class="flex items-center gap-[2.2rem]"><img src="{{ asset('images/icons/location.svg') }}" alt="" class="w-[1.9rem] h-[1.9rem] object-contain"><span>{{ $address }}</span></li>
    @endif
  </ul>
  @if (! empty($settings->social_media_accounts))
    <div class="mt-[3rem] lg:mt-0 lg:absolute lg:top-[14.5rem] lg:right-[94.3rem] flex items-center gap-[1.8rem]">
      @foreach ($settings->social_media_accounts as $account)
        @php $icon = $socialIcons[$account['platform'] ?? ''] ?? null; @endphp
        <a href="{{ $account['url'] ?? '#' }}" target="_blank" rel="noopener" aria-label="{{ $account['handle'] ?? $account['platform'] ?? '' }}">
          @if ($icon)
            <img src="{{ asset($icon) }}" alt="" class="w-[2.4rem] h-[2.4rem]">
          @else
            <span class="text-[1.4rem]">{{ $account['handle'] ?? $account['platform'] ?? '' }}</span>
          @endif
        </a>
      @endforeach
    </div>
  @endif
  <p class="mt-[4rem] lg:mt-0 lg:absolute lg:top-[9.9rem] lg:right-[150.4rem] m-0 text-[1.4rem] leading-[2rem] tracking-[0.111rem]">{{ __('site.footer.copyright', ['year' => date('Y')]) }}</p>
</footer>
