{{-- Design: article.html. The pull quote is placed after the first paragraph of the first section (after the first
     heading), the summary box before the second heading, as in the design; the last paragraph ends with the ornament. --}}
@php
    $a = $article;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $ornament = Vite::asset('resources/site/assets/svg/ornament.svg');
    $orn = '<img src="'.e($ornament).'" alt="">';
    $w = $a->writer;

    // Body: headings get the ornament, then the pull quote and the box are placed, the last paragraph is the "end".
    $html = str_replace('<h2>', '<h2>'.$orn, (string) str((string) $a->body)->sanitizeHtml());
    $pull = $a->pull_quote ? '<blockquote class="ar-pull"><span aria-hidden="true">”</span>'.e($a->pull_quote).'</blockquote>' : '';
    $items = $a->boxItems();
    $box = $items ? '<aside class="ar-box" aria-label="'.e(__('site.magazine.box')).'">'.($a->box_title ? '<p class="ar-box-t">'.e($a->box_title).'</p>' : '')
        .'<ol>'.collect($items)->map(fn ($i) => '<li>'.e($i).'</li>')->implode('').'</ol></aside>' : '';
    $parts = preg_split('#(?=<h2>)#u', $html);           // [intro, section 1, section 2, ...]
    if ($pull) {
        $at = count($parts) > 1 ? 1 : 0;
        $parts[$at] = preg_replace('#</p>#u', '</p>'.$pull, $parts[$at], 1, $placed);
        if (! $placed) { $parts[$at] .= $pull; }
    }
    if ($box) {
        count($parts) > 2 ? $parts[1] .= $box : $parts[count($parts) - 1] .= $box;
    }
    $html = implode('', $parts);
    $html = preg_replace_callback('#<p>((?:(?!<p>).)*)</p>(?!.*<p>)#su', fn ($m) => '<p class="ar-end">'.$m[1].' '.$orn.'</p>', $html, 1);

    $byline = collect([$a->dateLabel(), $a->issue?->label()])->filter()->implode(' · ');
    $shareText = rawurlencode($a->title);
@endphp
<x-site.layout :title="$a->title" :description="$a->dek" :image="$a->imageUrl('cover')" styles="article" og-type="article" :noindex="! $a->isLive()"
    :crumb="['title' => $page->title, 'url' => route('magazine.index'), 'sub' => null]">
<div class="ar-progress" aria-hidden="true"><i data-progress></i></div>
<article class="mag-page ar-page" data-article-view="{{ route('magazine.view', $a) }}">
  <header class="wrap ar-head">
    <nav class="ar-crumbs" aria-label="{{ __('site.magazine.crumbs') }}"><a href="{{ route('magazine.index') }}">{{ $page->title }}</a>@if ($a->category)<span aria-hidden="true">‹</span><span>{{ $a->category->name }}</span>@endif</nav>
    <p class="kick">@if ($isLead){{ __('site.magazine.issue_lead') }} · @endif{{ $a->category?->name }}</p>
    <h1>{{ $a->title }}</h1>
    @if ($a->dek)<p class="ar-dek">{{ $a->dek }}</p>@endif
    <div class="byline">
      <span class="av" aria-hidden="true">@if ($ph = $w?->photoUrl())<img src="{{ $ph }}" alt="">@endif</span>
      <span>@if ($w)<b>{{ $w->name }}</b>@endif<small>{{ $byline }}</small></span>
      <span class="ring" style="--p:{{ min(1, $a->read_minutes / 10) }}" title="{{ __('site.magazine.read_time', ['n' => $d($a->read_minutes)]) }}"><b>{{ $d($a->read_minutes) }}</b><small>{{ __('site.magazine.minutes_unit') }}</small></span>
    </div>
  </header>

  @if ($cover = $a->image('cover'))
    <figure class="ar-cover">
      <img src="{{ $a->imageUrl('cover') }}" alt="{{ \App\Models\Article::alt($cover) }}">
      @if ($a->cover_caption)<figcaption>{{ $a->cover_caption }}</figcaption>@endif
    </figure>
  @endif

  <div class="wrap ar-body">
    @if ($a->lead)<p class="ar-lead">{{ $a->lead }}</p>@endif
    {!! $html !!}

    @if ($tags = $a->tagList())
      <div class="ar-tags">@foreach ($tags as $t)<a href="{{ route('magazine.index', ['q' => ltrim($t, '#')]) }}#mag-all">#{{ ltrim($t, '#') }}</a>@endforeach</div>
    @endif

    <div class="ar-share">
      <span>{{ __('site.magazine.share') }}</span>
      <button type="button" data-share aria-label="{{ __('site.magazine.share_btn') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg></button>
      <a href="https://x.com/intent/post?text={{ $shareText }}&amp;url={{ rawurlencode($a->url()) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('site.magazine.share_x') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l16 16M20 4 4 20M4 4h4l12 16h-4Z"/></svg></a>
      <button type="button" data-copy aria-label="{{ __('site.magazine.copy') }}" data-done="{{ __('site.magazine.copied') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg></button>
      <small class="ar-copied" role="status" aria-live="polite"></small>
    </div>

    @if ($w)
      <section class="ar-author" aria-label="{{ __('site.magazine.about_writer') }}">
        @if ($ph = $w->imageUrl('photo'))<img src="{{ $ph }}" alt="{{ $w->name }}">@endif
        <div><small>{{ __('site.magazine.about_writer') }}</small><b>{{ $w->name }}</b>@if ($w->bio)<p>{{ $w->bio }}</p>@endif
          @if ($w->isLive())<a href="{{ $w->url() }}">{{ __('site.magazine.writer_articles') }} <span aria-hidden="true">←</span></a>@endif</div>
      </section>
    @endif
  </div>

  @if ($related->isNotEmpty())
    <section class="wrap mag-sec" aria-labelledby="rel-h">
      <h2 class="mag-h" id="rel-h"><img src="{{ $ornament }}" alt="">{{ __('site.magazine.read_also') }}</h2>
      <div class="mag-list">@foreach ($related as $r)<x-site.magazine.row :article="$r" />@endforeach</div>
    </section>
  @endif
</article>
</x-site.layout>
