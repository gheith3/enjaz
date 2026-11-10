{{-- Design: university.html (js/university.js rendered here; album and screening room by js uni-library). --}}
@php
    $o = $organization;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $kind = $o->kindLabel();
    $logo = $o->logoUrl();
    $sector = \App\Models\Organization::SECTORS[$o->sector] ?? null;
    $chips = array_values(array_filter([
        $sector ? __('site.university.sector_'.($o->academic_group === 'college' ? 'college' : 'uni'), ['sector' => $sector]) : null,
        $o->founded ? __('site.university.founded', ['year' => $o->founded]) : null,
    ]));
    $presidents = $o->leaders->where('kind', 'president')->values();
    $leaders = $o->leaders->where('kind', 'leader')->values();
    $board = $o->leaders->where('kind', 'board')->values();
    $colleges = $o->units->where('kind', 'college')->groupBy('year');
    $unitGroups = $o->units->where('kind', 'unit')->groupBy(fn ($u) => (string) $u->group_label);
    $centers = $o->units->where('kind', 'center');
    $branches = $o->units->where('kind', 'branch');
    $letter = $o->letter?->is_published ? $o->letter : null;
    $people = $o->achievers()->published()->with('media')->get();
    $rows = $achievements->map(fn ($r) => ['achievement' => $r['achievement'], 'type' => \App\Models\Organization::displayType($r['type'])]);
    $typeCount = fn ($t) => $rows->where('type', $t)->count();
    $photos = $o->getMedia('gallery');
    $hasLibrary = $photos->isNotEmpty() || $o->videos->isNotEmpty();
    $ord = __('site.university.ordinals');
    $now = (int) date('Y');
    $initials = fn ($name) => mb_substr(explode(' ', $name)[0], 0, 1).mb_substr(preg_replace('/^ال/u', '', (string) collect(explode(' ', $name))->last()), 0, 1);
    $about = array_values(array_filter(preg_split('/\R\s*\R/u', (string) $o->about)));
@endphp
<x-site.layout :title="$o->name" :description="$o->tagline ?: \Illuminate\Support\Str::limit(strip_tags((string) $o->about), 160)" :image="$o->imageUrl('cover') ?? $logo"
    styles="university" body-class="flush" :noindex="! $o->isLive()" class="u-page" data-uni-page data-org="{{ $o->slug }}"
    :crumb="['title' => $listPage->title, 'url' => route('universities.index'), 'sub' => $o->name]">
  <section class="u-hero" data-u-hero>
    <div class="uh-top"><div class="uh-txt">@if ($chips)<span class="uh-kick">{{ implode(' · ', $chips) }}</span>@endif
      <h1>{{ $o->name }}</h1>@if ($en = $o->getTranslation('name', 'en', false))<p class="uh-en" dir="ltr">{{ $en }}</p>@endif
      @if ($o->tagline || $o->city)<p class="uh-about">{{ collect([$o->tagline, $o->city])->filter()->implode(' · ') }}</p>@endif</div>
      <span @class(['uh-emb', 'is-plate' => ! $logo || $o->hasWideLogo()])>@if ($logo)<img src="{{ $logo }}" alt="{{ $o->name }}">@else<b>{{ $o->abbr ?: preg_replace('/[^A-Z]/', '', (string) $en) }}</b>@endif</span></div>
    @if ($o->stats->isNotEmpty())<div class="uh-stats">@foreach ($o->stats as $stat)<span><b>{{ $stat->value }}</b><small>{{ $stat->label }}</small></span>@endforeach</div>@endif
    <nav class="uh-jump" aria-label="{{ __('site.university.jump') }}">
      <a href="#u-about">{{ __('site.university.jump_about') }}</a>
      @if ($presidents->isNotEmpty() || $leaders->isNotEmpty())<a href="#u-pres">{{ __('site.university.jump_'.($presidents->isNotEmpty() ? 'presidents' : 'leaders')) }}</a>@endif
      <a href="#u-ach">{{ __('site.university.jump_achievements') }}</a>
      @if ($colleges->isNotEmpty() || $unitGroups->isNotEmpty())<a href="#u-colleges">{{ __('site.university.jump_colleges') }}</a>@endif
      <a href="#u-people">{{ __('site.university.jump_people') }}</a>
      @if ($hasLibrary)<a href="#u-lib">{{ __('site.university.jump_library') }}</a>@endif
    </nav>
  </section>
  <div class="wrap">
    <section class="u-sec" id="u-about" aria-labelledby="u-about-h"><h2 class="u-h" id="u-about-h">{{ __('site.university.about', ['kind' => $kind]) }}</h2><div>
      @if ($about)@foreach ($about as $paragraph)<p class="u-text">{{ $paragraph }}</p>@endforeach @else<p class="u-soon">{{ __('site.university.about_soon') }}</p>@endif
      @if ($centers->isNotEmpty())<div class="u-ctr"><b>{{ __('site.university.centers') }}</b><ul>@foreach ($centers as $center)<li>{{ $center->name }}</li>@endforeach</ul></div>@endif
      @if ($branches->isNotEmpty())<div class="u-br"><b>{{ __('site.university.branches') }}</b><div>@foreach ($branches as $branch)<span style="--i:{{ $loop->index }}">{{ $branch->name }}</span>@endforeach</div></div>@endif
      @if ($o->website)<a class="u-site" href="{{ $o->website }}" target="_blank" rel="noopener">{{ __('site.university.website') }} <span aria-hidden="true">↗</span></a>@endif
    </div></section>

    @if ($presidents->isNotEmpty())
      @php
          $y0 = $presidents->first()->from_year ?: $now; $span = max(1, $now - $y0);
          $dur = fn ($p) => ($p->to_year ?: $now) - ($p->from_year ?: $now);
          $longest = $presidents->keys()->reduce(fn ($m, $i) => $dur($presidents[$i]) > $dur($presidents[$m]) ? $i : $m, 0);
          $durText = fn ($p) => ($dur($p) <= 0 ? __('site.university.years_less') : trans_choice('site.university.years', $dur($p), ['n' => $dur($p)])).($p->is_current ? ' '.__('site.university.until_now') : '');
      @endphp
      <section class="u-sec u-pres" id="u-pres" aria-labelledby="u-pres-h">
        <div class="up-head"><span class="up-eyebrow">{{ __('site.university.leadership_record') }}</span><h2 id="u-pres-h">{{ __('site.university.presidents', ['kind' => $kind]) }}</h2></div>
        <div class="ur"><div class="ur-bar">@foreach ($presidents as $i => $p)<button type="button" @class(['ur-seg', 'is-now' => $p->is_current]) data-p="{{ $i }}" style="flex:{{ max($dur($p), .6) }}" aria-label="{{ ($ord[$i] ?? $i + 1).': '.$p->name }}"><span>{{ $i + 1 }}</span></button>@endforeach</div>
          <div class="ur-scale"><span>{{ $y0 }}</span><span>{{ (int) round($y0 + $span / 2) }}</span><span>{{ $now }}</span></div></div>
        <ol class="up-rail" data-u-pres>
          @foreach ($presidents as $i => $p)
            <li @class(['up-card', 'is-now' => $p->is_current]) data-p="{{ $i }}" tabindex="0">
              <span class="up-ord" aria-hidden="true">{{ $i + 1 }}</span>
              <span class="up-ph">@if ($photo = $p->image('photo')?->getUrl())<img src="{{ $photo }}" alt="{{ $p->name }}" loading="lazy">@endif</span>
              <span class="up-k">{{ __('site.university.president_n', ['ord' => $ord[$i] ?? $i + 1]) }}@if ($p->is_current) · {{ __('site.university.current') }}@endif</span>
              <small class="up-t">{{ $p->title }}</small><b class="up-n">{{ $p->name }}</b>
              <span class="up-y">{{ $p->from_year }} – {{ $p->to_year ?: __('site.university.now') }}</span><span class="up-d">{{ $durText($p) }}@if ($i === $longest && ! $p->is_current) · {{ __('site.university.longest') }}@endif</span>
              @if ($p->note)<small class="up-note">{{ $p->note }}</small>@endif
            </li>
          @endforeach
        </ol>
      </section>
    @elseif ($leaders->isNotEmpty())
      <section class="u-sec u-pres" id="u-pres" aria-labelledby="u-pres-h">
        <div class="up-head"><span class="up-eyebrow">{{ __('site.university.leadership_of', ['kind' => $kind]) }}</span><h2 id="u-pres-h">{{ __('site.university.board_title') }}</h2></div>
        <ol class="up-rail ul-lead">
          @foreach ($leaders as $p)
            <li @class(['up-card', 'is-now' => $p->is_current])>
              <span class="up-ph">@if ($photo = $p->image('photo')?->getUrl())<img src="{{ $photo }}" alt="{{ $p->name }}" loading="lazy">@else<i class="up-ini" aria-hidden="true">{{ $initials($p->name) }}</i>@endif</span>
              <span class="up-k">{{ $p->role }}</span><small class="up-t">{{ $p->title }}</small><b class="up-n">{{ $p->name }}</b>
              @if ($p->note)<small class="up-note">{{ $p->note }}</small>@endif
            </li>
          @endforeach
        </ol>
        @if ($board->isNotEmpty())
          <div class="ub"><h3 class="ub-h">{{ __('site.university.board_members') }} <small>{{ $board->count() }}</small></h3><div class="ub-grid">
            @foreach ($board as $m)
              <div class="ub-m"><span class="ub-ph">@if ($photo = $m->image('photo')?->getUrl())<img src="{{ $photo }}" alt="{{ $m->name }}" loading="lazy">@else<i>{{ $initials($m->name) }}</i>@endif</span>
                <small class="ub-t">{{ $m->title }}</small><b>{{ $m->name }}</b><span class="ub-r">{{ $m->role }}</span></div>
            @endforeach
          </div></div>
        @endif
      </section>
    @endif

    @if ($letter && $letter->image('photo'))
      <section class="u-sec u-word" id="u-word" aria-labelledby="u-word-h"><h2 class="u-h" id="u-word-h">{{ __('site.university.word', ['kind' => $kind]) }}</h2><div>
        <x-site.organization.word-d5 :letter="$letter" variant="leader" />
      </div></section>
    @endif

    <section class="u-sec un" id="u-ach" aria-labelledby="u-news-h">
      <div class="un-head">
        <span class="un-eyebrow">{{ __('site.university.news_eyebrow') }}</span>
        <h2 id="u-news-h">{{ __('site.university.achievements_of', ['name' => $o->name]) }}</h2>
        <div class="un-types">@foreach (['org' => 'type_org', 'student' => 'types_student', 'staff' => 'types_staff'] as $type => $label)<a href="{{ route('organizations.achievements', [$o, 'type' => $type]) }}"><b>{{ $d($typeCount($type)) }}</b>{{ __('site.university.'.$label) }}</a>@endforeach</div>
      </div>
      <div class="un-list">
        @forelse ($rows->take(7) as $i => $row)
          @php $a = $row['achievement']; $who = $a->primaryAchiever()?->name ?? $o->name; $meta = collect([$who, $a->dateLabel()])->filter()->implode(' · ');
              $img = $a->imageUrl('cover', $i ? 'card' : 'large'); $focal = \App\Models\Achievement::focal($a->image('cover')); @endphp
          @if ($i === 0)
            <a class="un-top" href="{{ $a->url() }}">@if ($img)<img src="{{ $img }}" alt="" style="object-position:{{ $focal }}">@endif
              <span class="un-top-b"><span class="un-k un-{{ $row['type'] }}">{{ __('site.university.type_'.$row['type']) }}</span><b>{{ $a->title }}</b><small>{{ $meta }}</small></span></a>
          @else
            <a class="un-row" href="{{ $a->url() }}"><span class="un-th">@if ($img)<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ $focal }}">@endif</span>
              <span class="un-b"><span class="un-k un-{{ $row['type'] }}">{{ __('site.university.type_'.$row['type']) }}</span><b>{{ $a->title }}</b><small>{{ $meta }}</small></span></a>
          @endif
        @empty
          <p class="u-soon">{{ __('site.university.no_achievements') }}</p>
        @endforelse
      </div>
      @if ($rows->isNotEmpty())<a class="un-bar" href="{{ route('organizations.achievements', $o) }}"><span>{{ __('site.university.all_achievements', ['kind' => $kind]) }}</span><b>{{ $d($rows->count()) }}</b><i aria-hidden="true">←</i></a>@endif
    </section>

    @if ($colleges->isNotEmpty())
      @php $total = 0; @endphp
      <section class="u-sec" id="u-colleges" aria-labelledby="u-col-h"><h2 class="u-h" id="u-col-h">{{ __('site.university.colleges_growth') }}</h2><ol class="ug">
        @foreach ($colleges as $year => $group)
          @php $total += $group->count(); $note = $group->first(fn ($c) => filled($c->note))?->note; @endphp
          <li><span class="ug-y">{{ $year }}</span><span class="ug-body"><span class="ug-chips">@foreach ($group as $college)<span>{{ $college->name }}</span>@endforeach</span>@if ($note)<small>{{ $note }}</small>@endif</span>
            <span class="ug-n" title="{{ __('site.university.colleges_count') }}"><b>{{ $total }}</b>{{ __('site.university.college_word_'.($total > 10 ? 'many' : 'few')) }}</span></li>
        @endforeach
      </ol></section>
    @elseif ($unitGroups->isNotEmpty())
      <section class="u-sec" id="u-colleges" aria-labelledby="u-col-h"><h2 class="u-h" id="u-col-h">{{ __('site.university.colleges_units') }}</h2><ol class="uu">
        @foreach ($unitGroups as $label => $group)
          <li><b class="uu-l">{{ $label }} <small>{{ $group->count() }}</small></b><span class="uu-list">@foreach ($group as $unit)<span>{{ $unit->name }}</span>@endforeach</span></li>
        @endforeach
      </ol></section>
    @endif

    @php $ranks = collect($o->rankings ?? []); @endphp
    @if ($ranks->isNotEmpty() || $o->milestones->isNotEmpty())
      <div class="u-top">
        @if ($ranks->isNotEmpty())
          @php $values = $ranks->pluck('rank')->map(fn ($r) => (int) $r); $worst = $values->max(); $best = $values->min(); @endphp
          <section class="u-sec" id="u-ranks" aria-labelledby="u-rk-h"><h2 class="u-h" id="u-rk-h">{{ __('site.university.ranks') }}</h2><div class="uk">
            <div class="uk-cols">@foreach ($ranks->values() as $i => $r)@php $h = 38 + 62 * ($worst === $best ? 1 : ($worst - (int) $r['rank']) / ($worst - $best)); $delta = $i ? (int) $ranks->values()[$i - 1]['rank'] - (int) $r['rank'] : 0; @endphp
              <span @class(['uk-col', 'is-last' => $loop->last])><span class="uk-bar" style="height:{{ round($h) }}%"><b>{{ $r['rank'] }}</b>@if ($delta > 0)<em>▲ {{ $delta }}</em>@endif</span><small>{{ $r['year'] }}</small></span>@endforeach</div>
            <p class="uk-l">{{ $o->ranking_label }} {{ __('site.university.ranks_hint') }}</p>
            @if ($highlights = collect($o->ranking_highlights ?? []))
              <div class="uk-hl">@foreach ($highlights as $h)<span><b>{{ \App\Support\Localized::get($h['text'] ?? null) }}</b>{{ \App\Support\Localized::get($h['subject'] ?? null) }}</span>@endforeach</div>
              @if ($o->ranking_note)<small class="uk-note">{{ $o->ranking_note }}</small>@endif
            @endif
          </div></section>
        @endif
        @if ($o->milestones->isNotEmpty())
          <section class="u-sec" id="u-ms" aria-labelledby="u-ms-h"><h2 class="u-h" id="u-ms-h">{{ __('site.university.milestones') }}</h2><ul class="um">
            @foreach ($o->milestones as $m)<li><span>{{ $m->year }}</span><b>{{ $m->title }}</b></li>@endforeach
          </ul></section>
        @endif
      </div>
    @endif

    <section class="u-sec" id="u-people" aria-labelledby="u-ppl-h">
      <h2 class="u-h" id="u-ppl-h">{{ __('site.university.people', ['kind' => $kind]) }} @if ($people->isNotEmpty())<small>{{ \App\Support\ArabicCount::of($people->count(), 'achievers') }}</small>@endif</h2>
      <p class="u-section-note">{{ __('site.university.people_note') }}</p>
      <div class="uq" data-u-people>
        @if ($people->isNotEmpty())
          <div class="us-grid">@foreach ($people->take(8) as $person)
            <a class="us" href="{{ $person->url() }}"><span class="us-ring"><span class="us-ph">@if ($img = $person->imageUrl('portrait', 'card'))<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achiever::focal($person->image('portrait')) }}">@endif</span></span>
              <b>{{ $person->name }}</b><small>{{ \App\Models\Organization::affiliationLabel($person->pivot->kind, $person->gender) }}</small></a>
          @endforeach</div>
          <a class="us-more" href="{{ route('organizations.people', $o) }}"><span>{{ __('site.university.people_all') }}</span><b>{{ $d($people->count()) }}</b><i aria-hidden="true">←</i></a>
        @else
          <p class="u-soon">{{ __('site.university.no_people') }}</p>
        @endif
      </div>
    </section>

    @if ($hasLibrary)
      <section class="u-sec lib" id="u-lib" aria-labelledby="u-lib-h" data-u-lib>
        <div class="lib-head"><span class="lib-eyebrow">{{ __('site.university.library_eyebrow') }}</span><h2 id="u-lib-h">{{ __('site.university.library_title', ['kind' => $kind]) }}</h2><span class="lib-sum" data-lib-sum></span></div>
        <div class="lib-album" data-lib-album>
          <h3 class="lib-h"><span>{{ __('site.university.album') }}</span><small>{{ __('site.university.album_note') }}</small></h3>
          <div class="lib-tabs" role="tablist" aria-label="{{ __('site.university.album_tabs') }}" data-lib-tabs></div>
          <div class="lib-wall" data-lib-wall></div>
        </div>
        <div class="lib-hall" data-lib-hall>
          <h3 class="lib-h"><span>{{ __('site.university.hall') }}</span><small>{{ __('site.university.hall_note') }}</small></h3>
          <div class="scr-frame"><div class="scr" data-scr></div></div>
          <div class="scr-info" data-scr-info></div>
          <h4 class="vt-h">{{ __('site.university.pick_clip') }}</h4>
          <ol class="vts" aria-label="{{ __('site.university.pick_video') }}" data-strip></ol>
        </div>
      </section>
      @php
          $credit = $o->gallery_credit ?? [];
          $library = [
              'chapters' => collect($o->gallery_chapters ?? [])->map(fn ($c) => [$c['key'], \App\Support\Localized::get($c['label'] ?? $c['key'])])->values()->all(),
              'credit' => filled($credit['text'] ?? null) ? [$credit['text'], $credit['url'] ?? ''] : null,
              'photos' => $photos->map(fn ($m) => [
                  'src' => $m->hasGeneratedConversion('large') ? $m->getUrl('large') : $m->getUrl(), 'chapter' => $m->getCustomProperty('chapter'),
                  'year' => $m->getCustomProperty('year'), 'title' => $m->getCustomProperty('title') ?? \App\Support\Localized::get($m->getCustomProperty('caption')),
                  'note' => $m->getCustomProperty('note'), 'doc' => $m->getCustomProperty('kind') === 'doc',
                  'credit' => $m->getCustomProperty('credit') ? [$m->getCustomProperty('credit'), $m->getCustomProperty('credit_url') ?? ''] : null,
              ])->values()->all(),
              'videos' => $o->videos->map(fn ($v) => ['yt' => $v->youtube_id, 'year' => $v->year, 'title' => $v->title, 'dur' => $v->duration, 'note' => $v->note, 'official' => $v->is_official])->values()->all(),
          ];
      @endphp
      <script>window.INJAZ_LIBRARY = {!! json_encode($library, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
    @endif

    @if ($o->sources->isNotEmpty())
      <p class="u-src">{{ __('site.university.sources') }} @foreach ($o->sources as $source)@if ($source->url)<a href="{{ $source->url }}" target="_blank" rel="noopener">{{ $source->title }}</a>@else{{ $source->title }}@endif{{ $loop->last ? '' : ' · ' }}@endforeach</p>
    @endif
    <a class="u-back" href="{{ route('universities.index') }}"><span aria-hidden="true">→</span> {{ __('site.university.back') }}</a>
  </div>
</x-site.layout>
