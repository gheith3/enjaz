{{-- Design: award-volunteer.html (js/award-volunteer.js + award-volunteer-winners.js). Podiums (phone) and the edition
     tabs (desktop) are rendered here; js award-volunteer switches tabs and opens a winner's card from the JSON below. --}}
@php
    $a = $award;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n ?? '');
    $cats = $a->categoriesOf('category');
    $fields = $a->categories->where('kind', 'field')->values();
    $editions = $a->editions->sortByDesc('sort_order')->values();
    $withWinners = $editions->filter(fn ($e) => $e->winners->isNotEmpty())->values();
    $all = $withWinners->flatMap(fn ($e) => $e->winners->reject->is_withheld->map(fn ($w) => [$w, $e]))->values();
    $index = $all->mapWithKeys(fn ($x, $i) => [$x[0]->id => $i]);
    $ord = __('site.awards.ordinals_f');
    $rank = __('site.awards.ranks');
    [$dateBold, $dateRest] = array_pad(explode('|', (string) $a->date_line, 2), 2, '');
    $page = \App\Models\Page::forKey('awards');
    $winnerOf = fn ($e, $key, $r) => $e->winners->first(fn ($w) => $w->category?->key === $key && $w->rank === $r);
    $pic = function ($w) {
        $photo = $w->image('photo');

        return $photo ? ['src' => $w->imageUrl('photo', 'card'), 'pos' => \App\Models\AwardWinner::focal($photo), 'photo' => true]
            : (($logo = $w->image('logo')) ? ['src' => $logo->getUrl(), 'pos' => null, 'photo' => false] : null);
    };
@endphp
<x-site.layout :title="$a->name" :description="\Illuminate\Support\Str::limit((string) $a->about, 160)" :image="$a->imageUrl('logo')"
    styles="award-volunteer" body-class="flush" :noindex="! $a->isLive()" class="v-page" data-vol
    :crumb="['title' => $page->title, 'url' => route('awards.index'), 'sub' => $a->label('field_label')]">
  <section class="v-hero" aria-labelledby="v-h1">
    <div class="v-ripple" aria-hidden="true"><i></i><i></i><i></i><i></i>
      <span class="v-core"><svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M32 52S14 41 14 27a10 10 0 0 1 18-6 10 10 0 0 1 18 6c0 14-18 25-18 25z"/><path d="M8 40c6 0 9 3 13 7M56 40c-6 0-9 3-13 7"/></svg></span></div>
    @if ($a->motto)<span class="v-eyebrow">«{{ $a->motto }}»</span>@endif
    <h1 id="v-h1">{{ $a->title ?: $a->name }}@if ($a->subtitle)<small>{{ $a->subtitle }}</small>@endif</h1>
    @if ($a->date_line)<p class="v-date"><b>{{ $dateBold }}</b><i></i>{{ $dateRest }}</p>@endif
    <div class="v-stats">
      @foreach ([[$d($editions->count()), __('site.awards.stat_vol_editions', ['year' => $a->founded_year])], [$d($editions->sum('entries_count')), __('site.awards.stat_entries')], [$d($fields->count()), __('site.awards.stat_fields')], [$d($cats->count()), __('site.awards.stat_cats')]] as [$n, $label])
        <span><b>{{ $n }}</b><small>{{ $label }}</small></span>
      @endforeach
    </div>
    <nav class="v-jump" aria-label="{{ __('site.awards.jump') }}"><a href="#v-about">{{ __('site.awards.jump_about') }}</a><a href="#v-how">{{ __('site.awards.jump_vol_how') }}</a><a href="#v-win">{{ __('site.awards.jump_winners') }}</a>@if ($a->sources->isNotEmpty())<a href="#v-src">{{ __('site.awards.jump_sources') }}</a>@endif</nav>
  </section>
  <div class="wrap">
    <section class="v-sec" id="v-about" aria-labelledby="v-about-h">
      <h2 class="v-h" id="v-about-h">{{ __('site.awards.about') }}</h2>
      <p class="v-lead">{{ $a->about }}</p>
      @if ($a->vision)<div class="v-vision"><span>{{ __('site.awards.vision') }}</span><p>{{ $a->vision }}</p></div>@endif
      <ul class="v-goals">@foreach ($a->factsIn('goals') as $goal)<li>{{ $goal->value }}</li>@endforeach</ul>
    </section>
    <section class="v-sec" id="v-how" aria-labelledby="v-how-h">
      <h2 class="v-h" id="v-how-h">{{ $a->label('how_title') }}</h2>
      <div class="v-cats">
        @foreach ($cats as $key => $cat)<div class="vc vc-{{ $key }}"><span class="vc-ic"><x-site.award.icon :name="$key" width="1.8" /></span><b>{{ $cat->name }}</b><small>{{ $cat->hint }}</small><em>{{ __('site.awards.places') }}</em></div>@endforeach
        @if ($a->honor)<p class="v-honor">{{ $a->honor }}</p>@endif
      </div>
      @if ($fields->isNotEmpty())
        <h3 class="v-sub">{{ $a->label('fields_title') }}</h3>
        <ul class="v-fields">@foreach ($fields as $i => $field)<li style="--i:{{ $i }}">{{ $field->name }}</li>@endforeach</ul>
      @endif
      <h3 class="v-sub">{{ __('site.awards.criteria') }}</h3>
      <ol class="v-crit">@foreach ($a->factsIn('criteria') as $i => $c)<li><span>{{ $d($i + 1) }}</span><b>{{ $c->label }}</b><small>{{ $c->value }}</small></li>@endforeach</ol>
    </section>

    <section class="v-sec" id="v-win" aria-labelledby="v-win-h">
      <div class="v-win-head">@if ($eyebrow = $a->label('winners_eyebrow'))<span class="v-eyebrow">{{ $eyebrow }}</span>@endif<h2 id="v-win-h">{{ $a->label('winners_title') }}</h2>@if ($note = $a->label('winners_note'))<p>{{ $note }}</p>@endif</div>
      {{-- Phone: podiums per edition --}}
      <ol class="v-eds">
        @foreach ($withWinners as $e)
          <li class="ve"><div class="ve-h"><span class="ve-year">{{ $e->year }}</span><div><b>{{ $e->label() }}</b><small>{{ __('site.awards.entries', ['n' => $d($e->entries_count)]) }}</small></div></div>
            @if ($cover = $e->image('cover'))<figure class="ve-cover"><img src="{{ $e->imageUrl('cover') }}" alt="{{ \App\Support\Localized::get($cover->getCustomProperty('caption')) }}" loading="lazy"><figcaption>{{ \App\Support\Localized::get($cover->getCustomProperty('caption')) }}<span>{{ __('site.awards.photo_credit', ['credit' => $cover->getCustomProperty('credit')]) }}</span></figcaption></figure>@endif
            @if ($e->is_partial)<p class="ve-partial">{{ __('site.awards.partial') }}</p>@endif
            @foreach ($cats as $key => $cat)
              @php $has = $e->winners->contains(fn ($w) => $w->category?->key === $key); @endphp
              <div class="vp vp-{{ $key }}"><h4><x-site.award.icon :name="$key" width="1.8" />{{ __('site.awards.category', ['name' => $cat->name]) }}</h4>
                @if (! $has && $e->is_partial)<p class="vp-none">{{ __('site.awards.no_category') }}</p>
                @else<ol class="vp-row">
                  @foreach ([2, 1, 3] as $r)
                    @php $w = $winnerOf($e, $key, $r); @endphp
                    @if (! $w)
                      @if ($e->is_partial)<li class="vp-s r{{ $r }} is-off"><span class="vp-who"><i class="vp-av">؟</i><b>{{ __('site.awards.pending') }}</b><small>{{ __('site.awards.pending_src') }}</small></span><span class="vp-blk"><strong>{{ $d($r) }}</strong></span></li>@endif
                    @elseif ($w->is_withheld)
                      <li class="vp-s r{{ $r }} is-off"><span class="vp-who"><i class="vp-av">—</i><b>{{ __('site.awards.withheld') }}</b><small>{{ __('site.awards.withheld_note') }}</small></span><span class="vp-blk"><strong>{{ $d($r) }}</strong></span></li>
                    @else
                      @php $p = $pic($w); @endphp
                      <li class="vp-s r{{ $r }}"><button type="button" data-vw="{{ $index[$w->id] }}"><span class="vp-who">
                        @if ($p)<i @class(['vp-av has-img', 'is-logo' => ! $p['photo']])><img src="{{ $p['src'] }}" alt="" loading="lazy" @if ($p['pos']) style="object-position:{{ $p['pos'] }}" @endif></i>@else<i class="vp-av"><x-site.award.icon :name="$key" width="1.8" /></i>@endif
                        <b>{{ $w->name }}</b><small>«{{ $w->work }}»</small></span><span class="vp-blk"><strong>{{ $d($r) }}</strong></span></button></li>
                    @endif
                  @endforeach
                </ol>@endif
              </div>
            @endforeach
            @if ($e->note)<p class="ve-note">{{ $e->note }}</p>@endif
          </li>
        @endforeach
      </ol>
      {{-- Desktop: one tab per edition (design award-volunteer-winners.js) --}}
      @if ($withWinners->isNotEmpty())
        <div class="lw" data-lw>
          <p class="lw-hint"><span aria-hidden="true">✦</span> {{ __('site.awards.pick_edition') }}</p>
          <div class="lw-tabs" role="tablist" aria-label="{{ __('site.awards.editions') }}">
            @foreach ($withWinners as $i => $e)<button type="button" role="tab" id="lw-t{{ $i }}" aria-controls="lw-p{{ $i }}" aria-selected="{{ $i ? 'false' : 'true' }}" @if ($i) tabindex="-1" @endif><b>{{ $e->year }}</b><span>{{ $e->label() }}</span><small>{{ __('site.awards.entries_short', ['n' => $d($e->entries_count)]) }}</small></button>@endforeach
          </div>
          @foreach ($withWinners as $i => $e)
            <section class="lw-p" role="tabpanel" id="lw-p{{ $i }}" aria-labelledby="lw-t{{ $i }}" @if ($i) hidden @endif>
              <header class="lw-ph">@if ($cover = $e->image('cover'))<figure><img src="{{ $e->imageUrl('cover') }}" alt="{{ \App\Support\Localized::get($cover->getCustomProperty('caption')) }}" loading="lazy"><figcaption>{{ __('site.awards.photo_credit', ['credit' => $cover->getCustomProperty('credit')]) }}</figcaption></figure>@endif
                <div><span class="lw-kick">{{ __('site.awards.tab_kicker', ['ord' => $ord[$e->number] ?? $e->number, 'date' => $dateBold.' '.$d($e->year)]) }}</span><h3>{{ __('site.awards.tab_title', ['n' => $d($e->entries_count)]) }}</h3>
                  @if ($e->note)<p>{{ $e->note }}</p>@endif @if ($e->is_partial)<p class="lw-partial">{{ __('site.awards.partial') }}</p>@endif</div></header>
              <div class="lw-cols">
                @foreach ($cats as $key => $cat)
                  @php $has = $e->winners->contains(fn ($w) => $w->category?->key === $key); @endphp
                  @if (! $has && $e->is_partial)
                    <div class="lw-col"><h4><x-site.award.icon :name="$key" width="1.8" />{{ $cat->name }}</h4><p class="lw-none">{{ __('site.awards.no_category') }}</p></div>
                  @else
                    <div class="lw-col lw-{{ $key }}"><h4><x-site.award.icon :name="$key" width="1.8" />{{ __('site.awards.category', ['name' => $cat->name]) }}</h4><ol>
                      @foreach ([1, 2, 3] as $r)
                        @php $w = $winnerOf($e, $key, $r); @endphp
                        @if (! $w)
                          @if ($e->is_partial)<li class="lw-w r{{ $r }} is-off"><span class="lw-rank">{{ $d($r) }}</span><span class="lw-txt"><b>{{ __('site.awards.rank_pending', ['rank' => $rank[$r]]) }}</b><small>{{ __('site.awards.rank_pending_note') }}</small></span></li>@endif
                        @elseif ($w->is_withheld)
                          <li class="lw-w r{{ $r }} is-off"><span class="lw-rank">{{ $d($r) }}</span><span class="lw-txt"><b>{{ __('site.awards.rank_withheld', ['rank' => $rank[$r]]) }}</b><small>{{ __('site.awards.rank_withheld_note') }}</small></span></li>
                        @else
                          @php $p = $pic($w); @endphp
                          <li class="lw-w r{{ $r }}"><span class="lw-rank" title="{{ $rank[$r] }}">{{ $d($r) }}</span>
                            <span @class(['lw-pic', 'is-logo' => $p && ! $p['photo'], 'is-icon' => ! $p])>@if ($p)<img src="{{ $p['src'] }}" alt="" loading="lazy" @if ($p['pos']) style="object-position:{{ $p['pos'] }}" @endif>@else<x-site.award.icon :name="$key" width="1.8" />@endif</span>
                            <span class="lw-txt"><small class="lw-rk">{{ $rank[$r] }}@if ($w->place) · {{ $w->place }}@endif</small><b>{{ $w->name }}</b>
                              <span class="lw-proj">{{ __('site.awards.project', ['name' => $w->work]) }}</span>
                              @if ($w->why)<span class="lw-why"><em>{{ __('site.awards.why') }}</em> {{ $w->why }}</span>@else<span class="lw-why is-none">{{ __('site.awards.no_why') }}</span>@endif
                              <button type="button" class="lw-more" data-vw="{{ $index[$w->id] }}">{{ __('site.awards.details') }} <span aria-hidden="true">←</span></button></span></li>
                        @endif
                      @endforeach
                    </ol></div>
                  @endif
                @endforeach
              </div>
            </section>
          @endforeach
        </div>
      @endif
      @php $early = $editions->filter(fn ($e) => $e->winners->isEmpty() && ! $e->is_partial); @endphp
      @if ($early->isNotEmpty())
        <h3 class="v-sub">{{ __('site.awards.early') }}</h3>
        <ol class="v-early">@foreach ($early as $e)<li><span class="ve-year">{{ $e->year }}</span><b>{{ $e->label() }}</b><small>{{ __('site.awards.entries_short', ['n' => $d($e->entries_count)]) }}</small><em>{{ __('site.awards.early_pending') }}</em></li>@endforeach</ol>
      @endif
    </section>
    @if ($a->sources->isNotEmpty())
      <section class="v-sec v-src" id="v-src" aria-labelledby="v-src-h"><h2 class="v-h" id="v-src-h">{{ __('site.awards.sources') }}</h2><ul>
        @foreach ($a->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
      </ul></section>
    @endif
  </div>
  <div class="v-sheet" data-v-sheet hidden><div class="vs-card" role="dialog" aria-modal="true" aria-labelledby="vs-h"></div></div>
  @php
      $winners = $all->map(function ($x) use ($ord, $pic) {
          [$w, $e] = $x;
          $photo = $w->image('photo');

          return [
              'name' => $w->name, 'rank' => $w->rank, 'cat' => $w->category?->key, 'catName' => $w->category?->name, 'project' => $w->work, 'place' => $w->place,
              'why' => $w->why, 'photo' => $photo ? $w->imageUrl('photo') : null, 'credit' => $photo?->getCustomProperty('credit'), 'pic' => $pic($w),
              'gallery' => $w->getMedia('gallery')->map(fn ($m) => [$m->hasGeneratedConversion('large') ? $m->getUrl('large') : $m->getUrl(), \App\Support\Localized::get($m->getCustomProperty('caption'))])->values(),
              'facts' => $w->facts->map(fn ($f) => [$f->label, $f->value])->values(),
              'src' => $w->source_url ? [$w->source_title ?: $w->source_url, $w->source_url] : null,
              'edition' => __('site.awards.edition_n', ['ord' => $ord[$e->number] ?? $e->number]).' · '.$e->year,
          ];
      })->values();
      $labels = collect(['why', 'no_why', 'close', 'source'])->mapWithKeys(fn ($k) => [$k => __('site.awards.'.$k)])
          ->put('ranks', __('site.awards.ranks'))->put('category', __('site.awards.category', ['name' => '%s']))
          ->put('project', __('site.awards.project', ['name' => '%s']))->put('photo', __('site.awards.photo_credit', ['credit' => '%s']));
  @endphp
  <script>window.INJAZ_VOL_WINNERS = {!! json_encode(['winners' => $winners, 'labels' => $labels], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
</x-site.layout>
