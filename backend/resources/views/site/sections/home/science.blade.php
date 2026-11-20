{{-- Home science section: expanding panels (js/sections/polish/home.js). Items: news marked «في الرئيسية» (up to three), then the latest
     inventions of the lab, four panels in all; the first is open. Without news, an older invention takes the first place as before. --}}
@php
    $news = \App\Models\News::query()->published()->where('show_on_home', true)->with(['media', 'category'])
        ->orderByDesc('happened_at')->orderBy('sort_order')->take(3)->get();
    $lead = $news->first();
    $lab = \App\Models\LabItem::query()->listed()->ofKind('invention')->with('media')->take(3)->get();
    $labLead = $lead ? null : \App\Models\LabItem::query()->listed()->ofKind('invention')->with('media')->skip(3)->first() ?? $lab->first();
    $fromNews = fn ($n) => ['url' => $n->url(), 'img' => $n->imageUrl('cover', 'large'), 'alt' => \App\Models\News::alt($n->image('cover')), 'field' => $n->tag,
        'title' => $n->title, 'who' => $n->summary, 'medal' => null, 'medal_label' => '', 'event' => ''];
    $fromLab = fn ($x) => ['url' => $x->url(), 'img' => $x->imageUrl('cover', 'large'), 'alt' => \App\Models\LabItem::alt($x->image('cover')) ?: $x->cardTitle(),
        'field' => $x->field ?: __('site.homepage.innovations'), 'title' => $x->cardTitle(), 'who' => $x->who, 'medal' => $x->medal, 'medal_label' => $x->medalLabel(),
        'event' => $x->event_label ?: $x->event];
    $items = $news->map($fromNews);
    if ($labLead) {
        $items->prepend($fromLab($labLead));
    }
    $items = $items->concat($lab->reject(fn ($x) => $x->is($labLead))->map($fromLab))->take(4)->values();
    $category = $lead?->category;
@endphp
@if ($items->isNotEmpty())
<section class="section wrap science-editorial sx" id="science" aria-labelledby="science-title" tabindex="-1">
  <div class="sx-head">
    <div class="sx-ttl">
      <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="science-title">{{ $section->text('title', __('site.homepage.science')) }}</h2></div>
      @if ($sub = $section->text('lead', __('site.homepage.science_lead')))<p class="science-subtitle">{{ $sub }}</p>@endif
    </div>
    <a class="sx-more" href="{{ $section->setting('more_url') ?: ($category?->url() ?? route('lab.index')) }}">{{ $section->text('more_label', __('site.homepage.science_more_btn')) }} <span aria-hidden="true">←</span></a>
  </div>
  <div class="sx-panels" data-sx>
    @foreach ($items as $i => $it)
      <a @class(['sx-p', 'is-on' => $i === 0]) href="{{ $it['url'] }}">
        @if ($it['img'])<img src="{{ $it['img'] }}" alt="{{ $it['alt'] }}" loading="lazy" decoding="async">@endif
        @if ($it['medal'] || $it['event'])<span class="sx-top">@if ($it['medal'])<span class="sx-medal {{ $it['medal'] }}"><i aria-hidden="true"></i>{{ $it['medal_label'] }}</span>@endif @if ($it['event'])<span class="sx-ev">{{ $it['event'] }}</span>@endif</span>@endif
        <span class="sx-body">
          @if ($it['field'])<span class="sx-f">{{ $it['field'] }}</span>@endif
          <strong class="sx-t">{{ $it['title'] }}</strong>
          @if ($it['who'])<span class="sx-who">{{ $it['who'] }}</span>@endif
          <span class="sx-read">{{ __('site.homepage.read_story') }} <i aria-hidden="true">←</i></span>
        </span>
      </a>
    @endforeach
  </div>
</section>
@endif
