@props(['achievement'])

@php
    /** @var \App\Models\Achievement $achievement */
    $tags = array_filter([
        $achievement->achievementMainType?->t('title'),
        $achievement->subAchievementType?->t('title'),
    ]);
    $excerpt = $achievement->t('excerpt') ?: \Illuminate\Support\Str::limit($achievement->t('description'), 220);
@endphp

<article {{ $attributes->merge(['class' => 'ach-card']) }}>
  @if ($achievement->cover_image_path)
    <img class="media" src="{{ \Illuminate\Support\Facades\Storage::url($achievement->cover_image_path) }}" alt="{{ $achievement->t('title') }}">
  @else
    <div class="media" style="background: linear-gradient(180deg, #096771, #053439);"></div>
  @endif
  @if ($tags)
    <div class="tags">
      @foreach ($tags as $tag)
        <span class="tag">{{ $tag }}</span>
      @endforeach
    </div>
  @endif
  @if ($achievement->achieved_at)
    <p class="date">{{ $achievement->achieved_at->format('j M Y') }}</p>
  @endif
  <h3 class="title">{{ $achievement->t('title') }}</h3>
  @if ($excerpt)
    <p class="desc">{{ $excerpt }}</p>
  @endif
  <a class="read" href="{{ route('achievements.show', $achievement) }}">{{ __('site.read_more') }}</a>
</article>
