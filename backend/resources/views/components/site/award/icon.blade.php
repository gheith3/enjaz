@props(['name', 'width' => '1.7'])
{{-- Line icons of the award designs (award.js, award-volunteer.js, bha-final.js). --}}
@php
    $paths = [
        'culture' => '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H11v15H5.5A1.5 1.5 0 0 1 4 17.5z"/><path d="M20 5.5c0-.8-.7-1.5-1.5-1.5H13v15h5.5c.8 0 1.5-.7 1.5-1.5z"/>',
        'arts' => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.6-.8 1.6-1.6 0-1.3-1.1-1.6-1.1-2.6 0-.9.7-1.6 1.6-1.6H16a5 5 0 0 0 5-5C21 6.4 17 3 12 3z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10.5" cy="7.3" r="1.2"/><circle cx="15" cy="7.8" r="1.2"/>',
        'lit' => '<path d="M19 4c-6 1-10.5 5.5-12 12l-2 4"/><path d="M19 4c.5 5-2.5 10-9 11.5"/><path d="M9 15.5 7 16"/>',
        'ind' => '<circle cx="12" cy="7.5" r="3.5"/><path d="M5 20c.8-4 3.6-6 7-6s6.2 2 7 6"/>',
        'org' => '<path d="M4 20V9l8-5 8 5v11"/><path d="M9 20v-6h6v6"/><path d="M4 20h16"/>',
        'hands' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
        'cal' => '<rect x="3.5" y="5" width="17" height="15" rx="2.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'globe' => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.6 3.6 5.4 3.6 8.5S14.5 17.9 12 20.5C9.5 17.9 8.4 15 8.4 12S9.5 6.1 12 3.5Z"/>',
        'pin' => '<path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0 1 13 0c0 5-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
        'build' => '<path d="M4 20V9l8-5 8 5v11"/><path d="M9 20v-6h6v6M3 20h18"/>',
        'flag' => '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>',
        'age' => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c.8-4 3.6-6 7-6s6.2 2 7 6"/>',
        'ruler' => '<path d="m3 17 14-14 4 4L7 21Z"/><path d="m7 13 2 2M10 10l2 2M13 7l2 2"/>',
        'team' => '<circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2.5 20c.6-3.4 2.6-5 5.5-5s4.9 1.6 5.5 5M10.5 20c.6-3.4 2.6-5 5.5-5s4.9 1.6 5.5 5"/>',
        'pc' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/>',
    ];
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $width }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes }}>{!! $paths[$name] ?? $paths['culture'] !!}</svg>
