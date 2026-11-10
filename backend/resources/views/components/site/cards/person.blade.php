@props(['achiever', 'cat' => null, 'headline' => null])
{{-- Achiever card of the design's achievers.html grid. --}}
@php $portrait = $achiever->image('portrait'); @endphp
<article class="ach person-card">
  <a class="ach-link" href="{{ $achiever->url() }}" aria-label="{{ $achiever->name }}"></a>
  @if ($portrait)
    <img class="ach-img" src="{{ $achiever->imageUrl('portrait', 'card') }}" alt="{{ \App\Models\Achiever::alt($portrait, $achiever->name) }}" width="236" height="236" loading="lazy" style="object-position:{{ \App\Models\Achiever::focal($portrait) }}">
  @else
    <img class="ach-img" src="{{ Vite::asset('resources/site/assets/svg/icon-omani-'.($achiever->isFemale() ? 'woman' : 'man').'-teal.svg') }}" alt="" width="236" height="236" loading="lazy">
  @endif
  <div class="ach-body">
    <p class="ach-cat">{{ $cat ?? $achiever->field?->name }}</p>
    <h3>{{ $achiever->name }}</h3>
    @if ($headline ?? $achiever->headline)<p class="ach-headline">{{ $headline ?? $achiever->headline }}</p>@endif
    <p class="ach-meta">{{ \App\Support\ArabicCount::of((int) ($achiever->achievements_count ?? $achiever->publishedAchievements()->count()), 'achievements') }}</p>
  </div>
</article>
