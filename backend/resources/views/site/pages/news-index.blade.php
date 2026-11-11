{{-- Design: news.html. The news of one category, each one read in full (anchors #news-<slug> are linked from the home page). --}}
@php
    $title = $category?->name ?: $page->title;
@endphp
<x-site.layout :title="$title" :description="$category?->intro ?: $page->meta_description" styles="news" body-class="news-page"
    :crumb="['title' => $page->title, 'url' => null, 'sub' => null]">
<section class="section wrap news-archive">
  <a class="news-back" href="{{ url('/') }}">{{ __('site.news.back_home') }}</a>
  <h1 id="news-page-title">{{ $title }}</h1>
  @if ($intro = $category?->intro ?: $page->hero_lead)<p class="topic-news-intro">{{ $intro }}</p>@endif
  @if ($items->isEmpty())
    <p class="topic-news-intro">{{ __('site.news.empty') }}</p>
  @else
    <div class="news-articles">
      @foreach ($items as $n)
        @php $cover = $n->image('cover'); @endphp
        <article class="news-article" id="{{ $n->anchor() }}" data-news-category="{{ $category?->slug }}">
          @if ($cover)<img class="news-article-image" src="{{ $n->imageUrl('cover') }}" alt="{{ \App\Models\News::alt($cover) }}" loading="lazy" @if ($w = $cover->getCustomProperty('width')) width="{{ $w }}" height="{{ $cover->getCustomProperty('height') }}" @endif>@endif
          <div class="news-article-copy">@if ($n->tag)<span class="topic-news-tag">{{ $n->tag }}</span>@endif<h2>{{ $n->title }}</h2>
            @if ($n->summary)<p class="news-article-summary">{{ $n->summary }}</p>@endif
            {!! str((string) $n->body)->sanitizeHtml() !!}
            @if ($n->cover_caption)<p class="news-article-note">{{ $n->cover_caption }}</p>@endif
          </div>
        </article>
      @endforeach
    </div>
  @endif
</section>
</x-site.layout>
