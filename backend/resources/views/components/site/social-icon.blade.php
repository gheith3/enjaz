@props(['platform'])
{{-- Decorative icons from the design's footer (design/index.html), plus a few extra platforms. --}}
<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
@switch($platform)
  @case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none"/>@break
  @case('whatsapp')<path d="M20.5 11.7a8.5 8.5 0 0 1-12.7 7.4L3 20.5l1.4-4.6A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.6 8.2c.4-.5.7-.5 1-.1l1.1 1.7c.2.3.1.6-.3 1 .6 1.2 1.5 2.1 2.7 2.7.4-.4.7-.5 1-.3l1.7 1.1c.4.3.4.6-.1 1-1.8 1.6-6.5-1.5-7.5-4.6-.4-1.1-.3-1.9.4-2.5Z"/>@break
  @case('x')<path d="M4 4l16 16M20 4 4 20M4 4h4l12 16h-4Z"/>@break
  @case('facebook')<path d="M14 21v-8h3l.5-3H14V8.5c0-.9.4-1.5 1.6-1.5H18V4.3A13 13 0 0 0 15.5 4C12.8 4 11 5.6 11 8.4V10H8v3h3v8"/>@break
  @case('linkedin')<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 10v6M8 7.5v.2M12 16v-6m0 2.5c0-3.2 4-3.4 4 0V16"/>@break
  @case('youtube')<rect x="3" y="6" width="18" height="12" rx="4"/><path d="m10 9 5 3-5 3Z"/>@break
  @case('snapchat')<path d="M12 3c3 0 5 2.2 5 5.2v2.3l1.8.6c.5.2.5.8 0 1l-1.9.8c.6 1.7 1.8 2.8 3.3 3.3-.6.8-1.8 1-2.8 1.1-.3.9-.6 1.4-1.4 1.2-1.2-.3-2.3.6-4 .6s-2.8-.9-4-.6c-.8.2-1.1-.3-1.4-1.2-1-.1-2.2-.3-2.8-1.1 1.5-.5 2.7-1.6 3.3-3.3l-1.9-.8c-.5-.2-.5-.8 0-1l1.8-.6V8.2C7 5.2 9 3 12 3Z"/>@break
  @case('tiktok')<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.4 2.6 2.2 4.4 5 4.6"/>@break
  @default<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/>
@endswitch
</svg>
