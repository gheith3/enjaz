{{-- Design index.html .home-articles: articles marked «في الرئيسية» (or the latest): one lead and two small. --}}
@php
    $list = \App\Models\Article::query()->listed()->where('show_on_home', true)->with(['media', 'writer.media'])->take(3)->get();
    if ($list->isEmpty()) {
        $list = \App\Models\Article::query()->listed()->with(['media', 'writer.media'])->take(3)->get();
    }
    $lead = $list->first();
    $by = fn ($a) => $a->writer ? __('site.homepage.writes', ['name' => $a->writer->name]) : '';
@endphp
@if ($lead)
<section class="section wrap home-articles" id="articles" aria-labelledby="articles-title">
  <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="articles-title">{{ $section->text('title', __('site.homepage.articles')) }}</h2></div>
  <div class="articles-board">
    <article class="article-lead">
      @if ($c = $lead->image('cover'))<img src="{{ $lead->imageUrl('cover') }}" alt="{{ \App\Models\Article::alt($c) }}" width="1536" height="1024" loading="lazy">@endif
      @if ($lead->writer)<p class="article-by">@if ($p = $lead->writer->photoUrl())<img class="article-av" src="{{ $p }}" alt="" width="40" height="40" loading="lazy">@endif{{ $by($lead) }}</p>@endif
      <h3><a href="{{ $lead->url() }}">{{ $lead->title }}</a></h3>
      @if ($lead->dek)<p>{{ $lead->dek }}</p>@endif
      <span class="article-read">{{ __('site.homepage.read_article') }}</span>
    </article>
    <div class="article-side">
      @foreach ($list->slice(1) as $a)
        <article class="article-mini"><a class="article-mini-link" href="{{ $a->url() }}">
          @if ($c = $a->image('cover'))<img src="{{ $a->imageUrl('cover', 'card') }}" alt="{{ \App\Models\Article::alt($c) }}" width="1536" height="1024" loading="lazy">@endif
          <div class="article-mini-copy">
            @if ($a->writer)<p class="article-by">@if ($p = $a->writer->photoUrl())<img class="article-av" src="{{ $p }}" alt="" width="40" height="40" loading="lazy">@endif{{ $by($a) }}</p>@endif
            <h3>{{ $a->title }}</h3>
            @if ($a->dek)<p>{{ $a->dek }}</p>@endif
            <span class="article-read">{{ __('site.homepage.read_article') }}</span>
          </div>
        </a></article>
      @endforeach
    </div>
  </div>
</section>
@endif
