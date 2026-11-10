@props(['crumb', 'logo', 'items'])
@php $desktopItems = $items->where('show_in_desktop_nav', true); @endphp
<header class="site-header{{ $desktopItems->isNotEmpty() ? ' has-desktop-nav' : '' }}">
  <div class="brand">
    <a class="logo" href="{{ url('/') }}" aria-label="{{ __('site.home') }}"><img src="{{ $logo }}" alt="{{ app(\App\Settings\GeneralSettings::class)->t('site_name') }}" width="117" height="94"></a>
    <span @class(['crumb', 'has-sub' => filled($crumb['sub']), 'is-link' => filled($crumb['url'])])
          data-page="{{ $crumb['title'] }}" aria-live="polite"
          @if (filled($crumb['url'])) data-href="{{ $crumb['url'] }}" role="link" tabindex="0" title="{{ __('site.back_to', ['section' => $crumb['title']]) }}" @endif>
      @if (filled($crumb['sub']))
        <span class="crumb-title">{{ $crumb['title'] }}</span><small class="crumb-sub">{{ $crumb['sub'] }}</small>
      @else
        {{ $crumb['title'] }}
      @endif
    </span>
  </div>
  @if ($desktopItems->isNotEmpty())
  <nav class="dnav" aria-label="{{ __('site.site_sections') }}"><ul>
    @foreach ($desktopItems as $item)
      <li><a href="{{ $item->href() }}" @if ($item->isCurrent()) aria-current="page" @endif @if ($item->opens_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $item->shortLabel() }}</a></li>
    @endforeach
  </ul></nav>
  <button type="button" class="dsearch" aria-label="{{ __('site.search_and_menu') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
  @endif
  <button class="menu-btn" type="button" aria-label="{{ __('site.open_menu') }}" aria-expanded="false" aria-controls="menu" data-menu-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
</header>
