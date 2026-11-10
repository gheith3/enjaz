@props(['achievement', 'variant' => 'grid', 'wide' => false])
{{-- One achievement card, three looks from the design:
     grid    = achievements.html list (field, name + date, summary)
     row     = "إنجازات أخرى" on the achievement page (tags, date, read more)
     profile = the achiever's profile grid (same as row, anchored by slug) --}}
@php
    $cover = $achievement->image('cover');
    $achiever = $achievement->primaryAchiever();
    $image = $achievement->imageUrl('cover', 'card') ?? $achiever?->imageUrl('portrait', 'card');
    $focal = $cover ? \App\Models\Achievement::focal($cover) : \App\Models\Achiever::focal($achiever?->image('portrait'));
    $tag = $variant === 'profile' ? ($achievement->profile_tag ?: $achievement->field?->name) : $achievement->disciplineLabel();
@endphp
<article @class(['ach', 'ach-row' => $variant === 'row', 'wide' => $wide]) @if ($variant === 'profile') id="ach-{{ $achievement->slug }}" @endif
    @if ($variant === 'grid' && $achievement->scope) data-scope="{{ $achievement->scope->slug }}" data-scope-label="{{ $achievement->scope->name }}" @endif
    @if ($variant === 'grid' && $achievement->field?->color) style="--c: {{ $achievement->field->color }}" @endif
    @if ($variant === 'profile') data-cat="{{ $tag }}" data-scope="{{ $achievement->scope?->cssGroup() }}" data-type="{{ $achievement->scope?->slug }}" data-date="{{ $achievement->achieved_at?->toDateString() }}" @endif>
  <a class="ach-link" href="{{ $achievement->url() }}" aria-label="{{ $achievement->title }}"></a>
  @if ($image)
    <img class="ach-img" src="{{ $image }}" alt="{{ $achievement->title }}" width="236" height="141" loading="lazy" style="object-position:{{ $focal }}">
  @endif
  <div class="ach-body">
    @if ($variant === 'grid')
      <p class="ach-cat">{{ $achievement->field?->name }}</p>
      <h3>{{ $achievement->title }}</h3>
      <p class="ach-meta"><span>{{ $achiever?->name }}</span><time datetime="{{ $achievement->achieved_at?->toDateString() }}">{{ $achievement->dateLabel() }}</time></p>
      <p class="desc">{{ $achievement->excerpt }}</p>
    @else
      <div class="tags">@if ($tag)<span class="tag">{{ $tag }}</span>@endif @if ($achievement->scope)<span class="tag scope-{{ $achievement->scope->cssGroup() }}">{{ $achievement->scope->name }}</span>@endif</div>
      <p class="date" @if ($variant === 'profile') dir="rtl" @endif>{{ $achievement->dateLabel() }}</p>
      <h3>{{ $achievement->title }}</h3>
      <p class="desc">{{ $achievement->excerpt }}</p>
      <a class="btn-read" href="{{ $achievement->url() }}"><span>{{ __('site.read_more') }}</span></a>
    @endif
  </div>
</article>
