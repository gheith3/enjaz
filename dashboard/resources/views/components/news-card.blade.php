@props(['item'])

@php
    /** @var \App\Models\News|\App\Models\Achievement $item */
    $isNews = $item instanceof \App\Models\News;
    $url = $isNews ? route('news.show', $item) : route('achievements.show', $item);
    $date = $isNews ? $item->published_at : $item->achieved_at;
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'news-card']) }}>
  @if ($item->cover_image_path)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->cover_image_path) }}" alt="{{ $item->t('title') }}">
  @endif
  @if ($date)
    <p class="date">{{ $date->format('j M Y') }}</p>
  @endif
  <h3>{{ $item->t('title') }}</h3>
</a>
