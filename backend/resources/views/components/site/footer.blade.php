@props(['general', 'logo', 'socials', 'columns'])
@php($phone = $general->phones[0] ?? null)
<footer class="site-footer footer-redesign">
  <div class="footer-layout">
    <nav class="footer-navigation" aria-label="{{ __('site.site_links') }}"><div class="footer-link-columns">
      @foreach ($columns as $column)
        <ul>
          @foreach ($column as $item)
            <li><a href="{{ $item->href() }}" @if ($item->opens_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a></li>
          @endforeach
        </ul>
      @endforeach
    </div></nav>
    <div class="footer-brand"><a class="footer-logo" href="{{ url('/') }}" aria-label="{{ $general->t('site_name') }} — {{ __('site.home') }}"><img src="{{ $logo }}" alt="{{ $general->t('site_name') }}"></a><div class="footer-socials" aria-label="{{ __('site.social_accounts') }}">
      @foreach ($socials as $social)
        @if ($social->url)
          <a href="{{ $social->url }}" aria-label="{{ $social->label() }}" target="_blank" rel="noopener noreferrer"><x-site.social-icon :platform="$social->platform" /></a>
        @else
          <span class="footer-social-unavailable" role="img" aria-label="{{ __('site.link_unavailable', ['platform' => $social->label()]) }}" title="{{ $social->label() }}"><x-site.social-icon :platform="$social->platform" /></span>
        @endif
      @endforeach
    </div></div>
    <div class="footer-details"><ul>
      @if ($phone)
        <li><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-phone-footer.svg') }}" alt=""><span dir="ltr">{{ $phone }}</span></a></li>
      @endif
      @if ($general->email)
        <li><a href="mailto:{{ $general->email }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-mail-footer.svg') }}" alt=""><span dir="ltr">{{ $general->email }}</span></a></li>
      @endif
      @if ($general->t('address'))
        <li><span><img src="{{ Vite::asset('resources/site/assets/svg/icon-pin-footer.svg') }}" alt="">{{ $general->t('address') }}</span></li>
      @endif
    </ul></div>
  </div>@if ($general->t('copyright'))<p class="copy">{{ $general->t('copyright') }}</p>@endif
</footer>
