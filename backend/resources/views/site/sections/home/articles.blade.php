{{-- Home .home-articles: one lead and four more beside it. Articles marked «في الرئيسية» come first, then the latest fill up to five. --}}
@php
    $take = 5;
    $with = ['media', 'writer.media', 'category'];
    $list = \App\Models\Article::query()->listed()->where('show_on_home', true)->with($with)->take($take)->get();
    if ($list->count() < $take) {
        $list = $list->concat(\App\Models\Article::query()->listed()->whereNotIn('id', $list->modelKeys())->with($with)->take($take - $list->count())->get());
    }
    $lead = $list->first();
    $kick = fn ($a) => ($a->category ? $a->category->name.' · ' : '').$a->minutesLabel();
@endphp
@if ($lead)
<section class="section wrap home-articles" id="articles" aria-labelledby="articles-title">
  <div class="am-head">
    <div class="am-ttl">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="articles-title">{{ $section->text('title', __('site.homepage.articles')) }}</h2></div>
      @if ($tag = $section->text('lead', __('site.homepage.articles_lead')))<p class="am-tag">{{ $tag }}</p>@endif
    </div>
    <a class="am-all" href="{{ route('magazine.index') }}">{{ $section->text('more_label', __('site.homepage.all_articles')) }} <span aria-hidden="true">←</span></a>
  </div>
  <div class="articles-board">
    <article class="article-lead">
      @if ($c = $lead->image('cover'))<img src="{{ $lead->imageUrl('cover') }}" alt="{{ \App\Models\Article::alt($c) }}" width="1536" height="1024" loading="lazy">@endif
      <p class="am-kick">{{ $kick($lead) }}</p>
      @if ($lead->writer)<p class="article-by">@if ($p = $lead->writer->photoUrl())<img class="article-av" src="{{ $p }}" alt="" width="40" height="40" loading="lazy">@endif<span><b>{{ $lead->writer->name }}</b>@if ($lead->writer->role)<small>{{ $lead->writer->role }}</small>@endif</span></p>@endif
      <h3><a href="{{ $lead->url() }}">{{ $lead->title }}</a></h3>
      @if ($lead->dek)<p>{{ $lead->dek }}</p>@endif
      <span class="article-read">{{ __('site.homepage.read_article') }}</span>
    </article>
    <ul class="am-list">
      @foreach ($list->slice(1) as $a)
        <li class="am-item"><a href="{{ $a->url() }}">
          @if ($c = $a->image('cover'))<span class="am-th"><img src="{{ $a->imageUrl('cover', 'card') }}" alt="{{ \App\Models\Article::alt($c) }}" width="1536" height="1024" loading="lazy"></span>@else<span class="am-th am-ph" aria-hidden="true"></span>@endif
          <span class="am-b">
            <span class="am-kick">{{ $kick($a) }}</span>
            <strong class="am-t">{{ $a->title }}</strong>
            @if ($a->writer)<span class="am-by">@if ($p = $a->writer->photoUrl())<img src="{{ $p }}" alt="" width="22" height="22" loading="lazy">@endif{{ $a->writer->name }}</span>@endif
          </span>
          <span class="am-go" aria-hidden="true">←</span>
        </a></li>
      @endforeach
    </ul>
  </div>
</section>
@endif
