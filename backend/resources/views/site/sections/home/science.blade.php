{{-- Design index.html .science-editorial: news marked «في الرئيسية» (one lead + two briefs) and the latest inventions of the
     lab (three more briefs, desktop only as in the design). --}}
@php
    $news = \App\Models\News::query()->published()->where('show_on_home', true)->with(['media', 'category'])
        ->orderByDesc('happened_at')->orderBy('sort_order')->take(3)->get();
    $lead = $news->first();
    $lab = \App\Models\LabItem::query()->listed()->ofKind('invention')->with('media')->take(3)->get();
    $briefs = $news->slice(1)->map(fn ($n) => ['url' => $n->url(), 'tag' => $n->tag, 'title' => $n->title, 'img' => $n->imageUrl('cover', 'card'), 'alt' => \App\Models\News::alt($n->image('cover')), 'dk' => false])
        ->concat($lab->map(fn ($x) => ['url' => $x->url(), 'tag' => __('site.homepage.innovations'), 'title' => $x->title, 'img' => $x->imageUrl('cover', 'card'), 'alt' => \App\Models\LabItem::alt($x->image('cover')), 'dk' => true]));
    $category = $lead?->category;
@endphp
@if ($lead || $briefs->isNotEmpty())
<section class="section wrap science-editorial" id="science" aria-labelledby="science-title" tabindex="-1">
  <div class="science-heading">
    <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="science-title">{{ $section->text('title', __('site.homepage.science')) }}</h2></div>
    @if ($sub = $section->text('lead', __('site.homepage.science_lead')))<p class="science-subtitle">{{ $sub }}</p>@endif
  </div>
  <div class="science-editorial-layout">
    @if ($lead)
      <article class="science-lead">
        <a href="{{ $lead->url() }}" class="science-lead-link">
          <div class="science-lead-photo">@if ($c = $lead->image('cover'))<img src="{{ $lead->imageUrl('cover') }}" alt="{{ \App\Models\News::alt($c) }}" loading="lazy" decoding="async" width="1536" height="1024">@endif<span class="science-lead-label">{{ __('site.homepage.spotlight_label') }}</span></div>
          <div class="science-lead-copy">@if ($lead->tag)<span class="science-story-category">{{ $lead->tag }}</span>@endif<h3>{{ $lead->title }}</h3>@if ($lead->summary)<p>{{ $lead->summary }}</p>@endif<span class="science-read">{{ __('site.homepage.read_story') }} <span aria-hidden="true">←</span></span></div>
        </a>
      </article>
    @endif
    <div class="science-briefs" aria-label="{{ __('site.homepage.science_more') }}">
      @foreach ($briefs as $b)
        <article @class(['science-brief', 'science-brief-dk' => $b['dk']])>
          <a href="{{ $b['url'] }}" class="science-brief-link">
            @if ($b['img'])<img src="{{ $b['img'] }}" alt="{{ $b['alt'] }}" loading="lazy" decoding="async">@endif
            <div class="science-brief-copy">@if ($b['tag'])<span class="science-story-category">{{ $b['tag'] }}</span>@endif<h3>{{ $b['title'] }}</h3><span class="science-read">{{ __('site.homepage.read_story') }} <span aria-hidden="true">←</span></span></div>
          </a>
        </article>
      @endforeach
    </div>
  </div>
  <div class="science-bottom"><a class="btn-more" href="{{ $section->setting('more_url') ?: ($category?->url() ?? route('lab.index')) }}">{{ $section->text('more_label', __('site.homepage.science_more_btn')) }}</a></div>
</section>
@endif
