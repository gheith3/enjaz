@props(['achievement'])
<dl class="story-facts" aria-label="{{ __('site.achievements.facts') }}">
  @if ($achievement->achieved_at)
    <div><dt><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 11h16"/></svg>{{ __('site.achievements.fact_year') }}</dt><dd>{{ $achievement->dateLabel() }}</dd></div>
  @endif
  @if ($achievement->disciplineLabel())
    <div><dt><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v15M12 5C8 2 3 4 3 4v15s5-2 9 1c4-3 9-1 9-1V4s-5-2-9 1Z"/></svg>{{ __('site.achievements.fact_field') }}</dt><dd>{{ $achievement->disciplineLabel() }}</dd></div>
  @endif
  @if ($achievement->country)
    <div><dt><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ __('site.achievements.fact_country') }}</dt><dd>{{ $achievement->country }}</dd></div>
  @endif
</dl>
