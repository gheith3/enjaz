{{-- Lab cards (design js/lab-list.js): variant "lb" = science.html phone lists and "others" on lab-item.html,
     "lz" = lab-all.html. Inventions show the photo and medal, publications the illustration and the finding. --}}
@props(['item', 'variant' => 'lb'])
@php
    $x = $item;
    $img = $x->imageUrl('cover', 'card');
    $tag = fn () => $x->cover_is_illustrative ? '<em>'.e(__('site.lab.illustrative')).'</em>' : '';
@endphp
@if ($variant === 'lz')
  @if ($x->isInvention())
    <a class="lz-card" href="{{ $x->url() }}"><span class="lz-img">@if ($img)<img src="{{ $img }}" alt="" loading="lazy">@endif<span class="lz-medal {{ $x->medal }}">{{ $x->medalLabel() }}</span></span>
      <span class="lz-body"><small>{{ $x->event_label }}@if ($x->place) • {{ $x->place }}@endif</small><b>{{ $x->cardTitle() }}</b>
        <span class="lz-who">{{ $x->who }}@if ($org = $x->orgName())<em>{{ $org }}</em>@endif</span>
        <span class="lz-foot"><time>{{ $x->dateLabel() }}</time><i>{{ __('site.lab.read_story_short') }}</i></span></span></a>
  @else
    <a class="lz-pub" href="{{ $x->url() }}"><span class="lz-pic">@if ($img)<img src="{{ $img }}" alt="" loading="lazy">@endif{!! $tag() !!}</span>
      <span class="lz-body"><span class="lz-top"><span class="lz-field">{{ $x->field }}</span><time>{{ $x->dateLabel() }}</time></span>
        <span class="lz-num">{{ $x->finding_value }}</span><b>{{ $x->title }}</b>
        <span class="lz-who">{{ $x->who }}@if ($x->journal)<em>{{ $x->journal }}</em>@endif</span>
        <span class="lz-foot"><i>{{ __('site.lab.read_summary') }}</i></span></span></a>
  @endif
@elseif ($x->isInvention())
  <a class="lb-card" href="{{ $x->url() }}"><span class="lb-img">@if ($img)<img src="{{ $img }}" alt="" loading="lazy">@endif<span class="lb-medal {{ $x->medal }}">{{ $x->medalLabel() }}</span></span>
    <span class="lb-body"><small class="lb-ev">{{ $x->event_label }}</small><b>{{ $x->cardTitle() }}</b><span class="lb-who">{{ $x->who }}</span><span class="lb-date">{{ $x->dateLabel() }}</span></span></a>
@else
  <a class="lb-card lb-pub" href="{{ $x->url() }}"><span class="lb-img lb-pub-img">@if ($img)<img src="{{ $img }}" alt="" loading="lazy">@endif{!! $tag() !!}</span>
    <span class="lb-pub-top"><span class="lb-field">{{ $x->field }}</span><span class="lb-date">{{ $x->dateLabel() }}</span></span>
    <span class="lb-num">{{ $x->finding_value }}</span><b>{{ $x->title }}</b><span class="lb-who">{{ $x->who }}</span></a>
@endif
