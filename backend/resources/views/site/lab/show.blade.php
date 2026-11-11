{{-- Design: lab-item.html (rendered by js/lab-list.js in the design): an invention story, or a publication with its
     finding, summary and "copy citation". --}}
@php
    $x = $item;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $inv = $x->isInvention();
    $segment = $x->segment();
    $cover = $x->image('cover');
    $minutes = $x->readMinutes();
    $read = match (true) { $minutes === 1 => __('site.lab.read_1'), $minutes === 2 => __('site.lab.read_2'), default => __('site.lab.read_n', ['n' => $d($minutes)]) };
@endphp
<x-site.layout :title="$x->title" :description="$inv ? $x->lead : $x->summary" :image="$x->imageUrl('cover')" styles="lab-item" og-type="article"
    :noindex="! $x->isLive()"
    :crumb="['title' => $page->label('crumb', __('site.lab.crumb')), 'url' => route('lab.index'), 'sub' => __('site.lab.'.$segment)]">
<article class="wrap lb-item" data-lab-item>
  @if ($inv)
    <header class="li-hero">@if ($cover)<img src="{{ $x->imageUrl('cover') }}" alt="{{ \App\Models\LabItem::alt($cover) }}">@endif<div class="li-hero-t">
      <span class="lb-medal {{ $x->medal }}">{{ $x->medalLabel() }} · {{ $x->event_label }}</span>
      <h1>{{ $x->title }}</h1><p class="li-who"><b>{{ $x->who }}</b>@if ($org = $x->orgName()) · @if ($x->organization?->isLive())<a href="{{ $x->organization->url() }}">{{ $org }}</a>@else{{ $org }}@endif @endif</p>
      <p class="li-meta"><span>{{ $x->place }}</span><span>{{ $x->dateLabel() }}</span><span>{{ $read }}</span></p></div></header>
    <div class="li-wrap">
      @if ($x->lead)<p class="li-lead">{{ $x->lead }}</p>@endif
      @if ($x->facts->isNotEmpty())
        <aside class="li-facts"><h2>{{ __('site.lab.facts') }}</h2><dl>@foreach ($x->facts as $f)<div><dt>{{ $f->label }}</dt><dd>{{ $f->value }}</dd></div>@endforeach</dl></aside>
      @endif
      <div class="li-body">{!! str((string) $x->body)->sanitizeHtml() !!}</div>
      @if ($x->credit_text)
        <p class="li-credit">{{ __('site.lab.photos') }} @if ($x->credit_url)<a href="{{ $x->credit_url }}" target="_blank" rel="noopener">{{ $x->credit_text }}</a>@else{{ $x->credit_text }}@endif</p>
      @endif
      @if ($x->sources->isNotEmpty())
        <div class="li-src"><h2>{{ __('site.lab.sources') }}</h2><ul>@foreach ($x->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach</ul></div>
      @endif
  @else
    <header class="li-hero li-hero-pub"><div class="li-hero-t"><span class="lb-field">{{ $x->field }}@if ($x->pub_type) · {{ $x->pub_type }}@endif</span>
      <h1>{{ $x->title }}</h1><p class="li-who">{{ $x->authors ?: $x->who }}</p><p class="li-meta"><span>{{ $x->journal }}</span><span>{{ $x->dateLabel() }}</span></p></div></header>
    <div class="li-wrap">
      @if ($x->finding_value)<div class="li-finding"><b>{{ $x->finding_value }}</b><span>{{ $x->finding_text }}</span></div>@endif
      <div class="li-body"><h2>{{ __('site.lab.summary') }}</h2><p>{{ $x->summary }}</p>@if ($x->body){!! str((string) $x->body)->sanitizeHtml() !!}@endif</div>
      <button type="button" class="li-cite" data-cite="{{ $x->citation() }}" data-done="{{ __('site.lab.cite_copied') }}">{{ __('site.lab.copy_cite') }}</button>
      @if ($x->sources->isNotEmpty())
        <div class="li-src"><h2>{{ __('site.lab.sources') }}</h2><ul>@foreach ($x->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach</ul></div>
      @endif
  @endif
      @if ($others->isNotEmpty())
        <h2 class="li-more-h">{{ __('site.lab.other_'.$segment) }}</h2><div class="lb-grid">@foreach ($others as $o)<x-site.lab.card :item="$o" />@endforeach</div>
      @endif
      <a class="lb-more" href="{{ route('lab.list', $segment) }}">{{ __('site.lab.all_'.$segment) }} <span aria-hidden="true">←</span></a>
    </div>
</article>
</x-site.layout>
