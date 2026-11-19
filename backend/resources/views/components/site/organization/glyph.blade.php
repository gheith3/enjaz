@props(['kind' => 'gov'])
{{-- Line icon of an organization type (OrganizationType::cssKey()): gov, mil, pvt, edu, sport. --}}
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
  @switch($kind)
    @case('mil')<path d="M12 2.8 19.5 5.6V11c0 4.6-3.2 8.3-7.5 9.4C7.7 19.3 4.5 15.6 4.5 11V5.6Z"/><path d="m12 7.6 1.3 2.7 2.9.4-2.1 2 .5 2.9-2.6-1.4-2.6 1.4.5-2.9-2.1-2 2.9-.4Z"/>@break
    @case('pvt')<rect x="3" y="7.5" width="18" height="12.5" rx="2.2"/><path d="M9 7.5V5.6A1.8 1.8 0 0 1 10.8 3.8h2.4A1.8 1.8 0 0 1 15 5.6v1.9M3 12.8h18M10.6 12.8v1.6h2.8v-1.6"/>@break
    @case('edu')<path d="M2.5 9 12 4.3 21.5 9 12 13.7Z"/><path d="M6.4 11v4.6c3.1 2.6 8.1 2.6 11.2 0V11M21.5 9v5.4"/>@break
    @case('sport')<path d="M8 2.8 11 9M16 2.8 13 9"/><circle cx="12" cy="14.6" r="6"/><path d="m12 11.7.9 1.9 2 .3-1.5 1.4.4 2-1.8-1-1.8 1 .4-2-1.5-1.4 2-.3Z"/>@break
    @default<path d="M3 20.5h18M4.5 9.5h15M12 3.2 20.5 8v1.5h-17V8Z"/><path d="M6.5 9.5v8.5M10 9.5v8.5M14 9.5v8.5M17.5 9.5v8.5M4 18h16"/>
  @endswitch
</svg>
