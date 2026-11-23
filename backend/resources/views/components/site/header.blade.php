@props(['crumb', 'logo', 'items', 'variant' => 'site'])
@php $desktopItems = $items->where('show_in_desktop_nav', true); @endphp
{{-- variant "about": the header of about.html (.page > .header, with the reading progress bar). --}}
<header class="{{ $variant === 'about' ? 'header' : 'site-header' }}{{ $desktopItems->isNotEmpty() ? ' has-desktop-nav' : '' }}">
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
  {{-- «شاركنا إنجازك» في أقصى يسار الشريط (كل الصفحات؛ css polish-global) --}}
  <a class="t-share" href="{{ url('/contact') }}">{{ __('site.share_achievement') }}</a>
  @endif
  @if ($variant === 'about')
  <button class="icon-btn" type="button" aria-label="{{ __('site.open_menu') }}" aria-expanded="false" aria-controls="menu" data-menu-open>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
  </button>
  <div class="progress" aria-hidden="true"><i data-progress></i></div>
  @else
  <button class="menu-btn" type="button" aria-label="{{ __('site.open_menu') }}" aria-expanded="false" aria-controls="menu" data-menu-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
  @endif
@if ($desktopItems->isNotEmpty())
<script>/* تصاميم ٢٠٤٠: يضبط مقاس أقسام الشريط قبل أول رسم (يوضع في آخر <header>) */
(function () {
  var h = document.currentScript && document.currentScript.closest('header');
  var n = h && h.querySelector('.dnav'), u = n && n.querySelector('ul');
  if (!u || !matchMedia('(min-width: 1100px)').matches) return;
  var set = function (v) { h.style.setProperty('--dp', v[0] + 'px'); h.style.setProperty('--df', v[1] + 'px'); h.style.setProperty('--dg', v[2] + 'px'); };
  var key = 'x-navfit2:' + innerWidth, saved = null;
  try { saved = sessionStorage.getItem(key); } catch (e) {}
  if (saved) { set(saved.split(',')); return; }
  var S = [[9, 13.5, 2], [8, 13.5, 2], [7, 13, 2], [6, 13, 1], [5, 12.5, 1], [4, 12, 0], [3, 11.5, 0]];
  for (var i = 0; i < S.length; i++) { set(S[i]); if (u.scrollWidth <= n.clientWidth + 1) break; }
})();
</script>
@endif
</header>
