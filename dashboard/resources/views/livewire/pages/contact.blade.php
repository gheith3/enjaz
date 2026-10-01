@section('title', __('site.contact.title'))
@section('footer')
    <!-- Contact page has no footer by design -->
@endsection

@php
    use Illuminate\Support\Facades\Storage;

    /** @var \App\Settings\GeneralSettings $settings */
    $socialIcons = [
        'instagram' => 'images/icons/instagram.svg',
        'twitter' => 'images/icons/twitter.svg',
        'x' => 'images/icons/twitter.svg',
        'linkedin' => 'images/icons/linkedin.svg',
    ];

    $socialAccounts = collect($settings->social_media_accounts)->map(function (array $account) use ($socialIcons) {
        $path = parse_url($account['url'] ?? '', PHP_URL_PATH) ?: '';
        $handle = trim(basename($path));

        return [
            'url' => $account['url'] ?? '#',
            'platform' => $account['platform'] ?? '',
            'icon' => $socialIcons[$account['platform'] ?? ''] ?? null,
            'label' => $handle !== '' ? '@'.$handle : ($account['platform'] ?? ''),
        ];
    })->filter(fn (array $account) => $account['url'] !== '#')->values();
@endphp

<!-- ===== Contact (single screen, 1920 x 1079, no footer) ===== -->
<header class="relative overflow-hidden min-h-screen lg:min-h-0 lg:h-[107.9rem] pt-[18rem] pb-[40rem] lg:p-0 bg-[linear-gradient(196deg,#0c7f89_0%,#0e4348_100%)]">
  <img src="{{ asset('images/brand/mountain-flag.svg') }}" alt="" aria-hidden="true" class="absolute left-1/2 -translate-x-1/2 lg:translate-x-0 lg:left-[56.6rem] bottom-0 lg:bottom-auto lg:top-[63.7rem] w-[90rem] lg:w-[138.2rem] max-w-none pointer-events-none">

  <div class="relative z-10 px-[4rem] lg:p-0 flex flex-col gap-[6rem] lg:block text-white">
    <!-- Form -->
    <div class="lg:absolute lg:top-[24.2rem] lg:right-[19.6rem] lg:w-[52.7rem]">
      <h1 class="m-0 text-[6.1rem] leading-[8rem] font-normal">{{ __('site.contact.heading') }}</h1>
      @if (! $submitted)
        <form wire:submit="submit" class="mt-[4.4rem]">
          <label class="contact-label" for="c-name">{{ __('site.contact.name') }}</label>
          <input id="c-name" wire:model="name" type="text" class="contact-field h-[4.7rem]">
          @error('name') <p class="m-0 mt-[0.8rem] text-[1.8rem] leading-[2.4rem] text-[#f4dba8]">{{ $message }}</p> @enderror

          <label class="contact-label mt-[2.5rem]" for="c-email">{{ __('site.contact.email') }}</label>
          <input id="c-email" wire:model="email" type="email" class="contact-field h-[4.5rem]">
          @error('email') <p class="m-0 mt-[0.8rem] text-[1.8rem] leading-[2.4rem] text-[#f4dba8]">{{ $message }}</p> @enderror

          <label class="contact-label mt-[2.5rem]" for="c-msg">{{ __('site.contact.message') }}</label>
          <textarea id="c-msg" wire:model="message" class="contact-field h-[15.5rem] resize-none !rounded-[2.6rem] py-[1.4rem]"></textarea>
          @error('message') <p class="m-0 mt-[0.8rem] text-[1.8rem] leading-[2.4rem] text-[#f4dba8]">{{ $message }}</p> @enderror

          <button type="submit" wire:loading.attr="disabled" class="mt-[4.9rem] mr-[1rem] w-[19rem] h-[5.9rem] rounded-full bg-[#a88344] hover:bg-[#967339] transition-colors border-0 cursor-pointer text-white text-[2.9rem] leading-[5.9rem] font-[inherit]">{{ __('site.contact.send') }}</button>
        </form>
      @else
        <div class="mt-[6rem] rounded-[3rem] bg-white/10 border border-white/40 p-[5rem] text-center">
          <svg class="w-[7rem] h-[7rem] mx-auto text-[#91c46c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="m8 12 3 3 5-6"/></svg>
          <p class="mt-[2rem] mb-0 text-[3.2rem] leading-[4.4rem] font-bold">{{ __('site.contact.success_title') }}</p>
          <p class="mt-[1rem] mb-0 text-[2.4rem] leading-[3.4rem] text-white/80">{{ __('site.contact.success_body') }}</p>
        </div>
      @endif
    </div>

    <span class="hidden lg:block absolute top-[34.2rem] left-[87.1rem] w-px h-[43.9rem] bg-white/[0.43]"></span>

    <!-- Socials, map, phone/email -->
    <div class="lg:absolute lg:top-[33.7rem] lg:left-[16.2rem] lg:w-[63.6rem]">
      @if ($socialAccounts->isNotEmpty())
        <ul class="list-none m-0 p-0 grid grid-cols-3 gap-[3.3rem]">
          @foreach ($socialAccounts as $account)
            <li>
              <a href="{{ $account['url'] }}" target="_blank" rel="noopener" class="social-box">
                @if ($account['icon'])
                  <img src="{{ asset($account['icon']) }}" alt="{{ $account['platform'] }}" class="h-[2.9rem]">
                @endif
                <span dir="ltr">{{ $account['label'] }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      @endif

      @if ($settings->map_image)
        <a href="{{ $settings->map_url ?: '#' }}" target="_blank" rel="noopener" class="block mt-[5.5rem] h-[24.2rem] rounded-[3rem] overflow-hidden">
          <img src="{{ Storage::url($settings->map_image) }}" alt="{{ __('site.contact.map_alt') }}" class="w-full h-full object-cover">
        </a>
      @endif

      <div class="mt-[5.5rem] flex flex-wrap items-center gap-x-[7rem] gap-y-[3rem] pr-[7rem] text-white">
        @if ($settings->phone || $settings->phone_secondary)
          <div class="flex items-center gap-[2.4rem]">
            <svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1L6.6 10.8Z"/></svg>
            <span dir="ltr" class="text-[1.8rem] leading-[2.4rem] text-right">
              @if ($settings->phone)
                <a href="tel:{{ $settings->phone }}" class="text-white no-underline">{{ $settings->phone }}</a>
              @endif
              @if ($settings->phone && $settings->phone_secondary)<br>@endif
              @if ($settings->phone_secondary)
                <a href="tel:{{ $settings->phone_secondary }}" class="text-white no-underline">{{ $settings->phone_secondary }}</a>
              @endif
            </span>
          </div>
        @endif
        @if ($settings->email)
          <a href="mailto:{{ $settings->email }}" class="flex items-center gap-[1.6rem] text-white no-underline">
            <img src="{{ asset('images/icons/email.svg') }}" alt="" class="w-[3.5rem]">
            <span dir="ltr" class="text-[2.9rem] leading-[4rem]">{{ $settings->email }}</span>
          </a>
        @endif
      </div>
    </div>
  </div>
</header>
