@props([
    'figure',
    'size' => 'lg',
])

@php
    /** @var \App\Models\PublicFigure $figure */
    $image = $figure->tile_image_path ?: $figure->image_path;
@endphp

<a href="{{ route('achievers.show', $figure) }}" {{ $attributes->merge(['class' => $size === 'sm' ? 'tile-sm' : 'tile']) }}>
  @if ($image)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" alt="{{ $figure->t('name') }}">
  @endif
  @if ($figure->achievementMainType)
    <span class="cat">{{ $figure->achievementMainType->t('title') }}</span>
  @endif
  <span class="name">{{ $figure->t('name') }}</span>
</a>
