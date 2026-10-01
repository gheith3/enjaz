@props(['title'])

<div {{ $attributes->merge(['class' => 'section-title']) }}>
  <img src="{{ asset('images/brand/mark.svg') }}" alt="" class="mark">
  <h2>{{ $title }}</h2>
</div>
