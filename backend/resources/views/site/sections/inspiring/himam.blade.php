{{-- Design inspiring.html .hm «هِمَمٌ بلا حدود» (js/inspiring-himam.js text size): the section's items (the first is the
     big one; tag = «الوصف القصير»، date = «بيانات إضافية») then news marked «همم بلا حدود». Title: | before the gold part. --}}
@php
    $items = $section->items()->with(['media', 'itemable'])->get()->map(fn ($it) => [
        'href' => $it->href() ?? '#', 'img' => $it->imageUrl('image', 'card'), 'alt' => \App\Models\PageSectionItem::alt($it->image('image')),
        'tag' => $it->label, 'title' => $it->displayTitle(), 'text' => $it->text, 'date' => \App\Support\Localized::get($it->settings['meta'] ?? null),
    ])->concat(\App\Models\News::query()->published()->where('show_in_himam', true)->with('media')->orderByDesc('happened_at')->get()->map(fn ($n) => [
        'href' => $n->url(), 'img' => $n->imageUrl('cover', 'card'), 'alt' => \App\Models\News::alt($n->image('cover')),
        'tag' => $n->tag, 'title' => $n->title, 'text' => $n->summary, 'date' => $n->dateLabel('month'),
    ]));
    $lead = $items->first();
    [$t1, $t2] = array_pad(explode('|', $section->text('title', __('site.inspiring.himam')), 2), 2, '');
@endphp
@if ($lead)
<section class="wrap hm" aria-labelledby="hm-h" data-hm>
  <header class="hm-head">
    <div>
      <span class="hm-kick"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4.5" r="2.2"/><path d="M10 8h3.2l.6 5H18l2.4 5.2-1.8.8-1.9-4H12.3L11 9.9"/><path d="M9.6 11.2a5.6 5.6 0 1 0 6.2 7.6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>{{ $section->text('eyebrow', __('site.inspiring.himam_kick')) }}</span>
      <h2 id="hm-h">{{ $t1 }} @if ($t2)<em>{{ $t2 }}</em>@endif</h2>
      @if ($lead_text = $section->text('lead'))<p>{{ $lead_text }}</p>@endif
    </div>
    <button type="button" class="hm-size" aria-pressed="false" data-hm-size><span aria-hidden="true">أ<small>أ</small></span>{{ __('site.inspiring.text_size') }}</button>
  </header>
  <div class="hm-grid">
    <a class="hm-lead" href="{{ $lead['href'] }}">
      <span class="hm-img">@if ($lead['img'])<img src="{{ $lead['img'] }}" alt="{{ $lead['alt'] }}" loading="lazy">@endif</span>
      <span class="hm-body">
        @if ($lead['tag'])<span class="hm-tag">{{ $lead['tag'] }}</span>@endif
        <b>{{ $lead['title'] }}</b>
        @if ($lead['text'])<span class="hm-txt">{{ $lead['text'] }}</span>@endif
        <span class="hm-meta"><time>{{ $lead['date'] }}</time><i>{{ __('site.inspiring.read_news') }}</i></span>
      </span>
    </a>
    @if ($items->count() > 1)
      <div class="hm-list">
        @foreach ($items->slice(1) as $it)
          <a class="hm-item" href="{{ $it['href'] }}">@if ($it['img'])<img src="{{ $it['img'] }}" alt="{{ $it['alt'] }}" loading="lazy">@endif
            <span>@if ($it['tag'])<span class="hm-tag">{{ $it['tag'] }}</span>@endif<b>{{ $it['title'] }}</b>@if ($it['date'])<time>{{ $it['date'] }}</time>@endif</span></a>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endif
