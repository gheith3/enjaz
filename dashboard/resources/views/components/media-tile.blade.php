@props([
    'title',
    'image' => null,
    'url' => '#',
])

<a href="{{ $url }}" aria-label="{{ $title }}" {{ $attributes->merge(['class' => 'media-item']) }}>
  @if ($image)
    <img src="{{ $image }}" alt="{{ $title }}">
  @endif
  @isset($icon)
    <span class="media-btn">{{ $icon }}</span>
  @endisset
</a>
