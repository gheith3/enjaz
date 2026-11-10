@props(['logo', 'items'])
<div class="menu" id="menu" role="dialog" aria-modal="true" aria-label="{{ __('site.main_menu') }}">
  <div class="menu-top">
    <a href="{{ url('/') }}" aria-label="{{ __('site.home') }}"><img src="{{ $logo }}" alt="{{ app(\App\Settings\GeneralSettings::class)->t('site_name') }}"></a>
    <button class="menu-close" type="button" aria-label="{{ __('site.close_menu') }}" data-menu-close><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 5l14 14M19 5L5 19"/></svg></button>
  </div>
  <form class="search" role="search" action="{{ url('/search') }}" method="get"><label class="sr-only" for="menu-q">{{ __('site.search_site') }}</label><input id="menu-q" name="q" type="search" placeholder="{{ __('site.search_site') }}"><button type="submit" aria-label="{{ __('site.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button></form>
  <nav aria-label="{{ __('site.site_sections') }}"><ul>
    @foreach ($items as $item)
      <li><a href="{{ $item->href() }}" @if ($item->isCurrent()) aria-current="page" @endif @if ($item->opens_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a></li>
    @endforeach
  </ul></nav>
</div>
