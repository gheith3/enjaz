{{-- Design: bilarab-award.html (its page was built by js/bha-final.js; rendered here). Motion: js bha-hero-motion,
     phone-arc-bilarab, scroll-cue and award-architecture (counters and reveal). --}}
@php
    $a = $award;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n ?? '');
    $editions = $a->editions->sortBy('sort_order')->values();
    $letter = $a->letter?->is_published ? $a->letter : null;
    $features = $a->features;
    $team = $editions->flatMap(fn ($e) => $e->winners->flatMap(fn ($w) => $w->teamNames() ?: [$w->name]))
        ->merge($features->flatMap(fn ($f) => $f->teamNames()))->filter()->unique();
    $entries = $editions->sum('entries_count') + $features->sum('entries_count');
    $stats = [[$editions->count(), trans_choice('site.awards.stat_cycles', $editions->count())], [$entries, __('site.awards.stat_participations')], [$team->count(), __('site.awards.stat_winners')]];
    $num = fn ($i) => $d(str_pad((string) $i, 2, '0', STR_PAD_LEFT));
    $head = fn ($n, $title, $id) => '<header class="c-head"><span class="c-no">'.e($num($n)).'</span><h2 id="'.$id.'">'.e($title).'</h2></header>';
    $bold = fn ($text) => preg_replace('/\*\*(.+?)\*\*/u', '<b>$1</b>', e((string) $text));
    $launch = preg_split('/\s+/u', trim((string) $a->launched));
    $who = $a->factsIn('who');
    $ranks = __('site.awards.arch_ranks');
    $pins = $editions->filter(fn ($e) => $e->map_x !== null)->map(fn ($e) => [$e->map_label, $e->map_x, $e->map_y])->values()->all();
    $caption = fn ($m) => \App\Support\Localized::get($m->getCustomProperty('caption'));
    $url = fn ($m) => \App\Support\Media\Src::of($m);
    $n = 0;
@endphp
<x-site.layout :title="$a->name" :description="\Illuminate\Support\Str::limit((string) $a->about, 160)" :image="$a->imageUrl('hero')"
    styles="award-architecture" body-class="flush" :noindex="! $a->isLive()" class="cx" data-c="2" data-bf
    :crumb="['title' => $a->label('crumb', $a->name), 'sub' => $a->subtitle]">
  <section class="c-hero" aria-labelledby="c-h1">
    @if ($hero = $a->imageUrl('hero'))<img class="bf-hero-img" src="{{ $hero }}" alt="" aria-hidden="true" width="2200" height="1466">@endif
    <div class="h-mash" aria-hidden="true"></div><div class="h-rays" aria-hidden="true"><i></i><i></i><i></i></div>
    <div class="c-hero-in">@if ($mark = $a->image('mark'))<img class="c-mark" src="{{ \App\Support\Media\Src::of($mark) }}" alt="" width="311" height="281">@endif
      @if ($a->eyebrow)<span class="c-eyebrow">{{ $a->eyebrow }}</span>@endif
      <h1 id="c-h1"><span class="gold-t bf-shine" data-t="{{ $a->title ?: $a->name }}">{{ $a->title ?: $a->name }}</span>@if ($a->subtitle)<small>{{ $a->subtitle }}</small>@endif</h1>
      @if ($a->motto)<p class="c-motto">«{{ $a->motto }}»</p>@endif
      <dl class="c-stats">@foreach ($stats as [$value, $label])<div><dt>{{ $label }}</dt><dd data-to="{{ $value }}">{{ $d($value) }}</dd></div>@endforeach</dl></div>
  </section>
  <nav class="c-jump" aria-label="{{ __('site.awards.jump') }}"><a href="#c-about">{{ __('site.awards.jump_idea') }}</a>@if ($who->isNotEmpty())<a href="#c-who">{{ __('site.awards.jump_who') }}</a>@endif @if ($letter)<a href="#c-word">{{ __('site.awards.jump_highness') }}</a>@endif<a href="#c-cycles">{{ __('site.awards.jump_cycles') }}</a>@if ($features->isNotEmpty())<a href="#c-more">{{ __('site.awards.jump_more') }}</a>@endif</nav>

  <section class="c-sec" id="c-about" aria-labelledby="c-about-h">{!! $head(++$n, __('site.awards.idea'), 'c-about-h') !!}
    <div class="bf-idea">
      @if ($a->lead)<p class="bf-idea-lead">{!! $bold($a->lead) !!}</p>@endif
      <ul class="bf-facts">
        @if ($a->label('blessing'))<li>@if ($emblem = $a->image('emblem'))<img class="bf-emblem" src="{{ \App\Support\Media\Src::of($emblem) }}" alt="">@endif<b>{{ $a->label('blessing') }}</b><small>{{ $a->label('blessing_by') }}</small></li>@endif
        @if ($a->launched)<li class="bf-launch"><span class="bf-cal" aria-hidden="true"><i>{{ $launch[1] ?? '' }}</i><b>{{ $d($launch[0] ?? '') }}</b><em>{{ $d($launch[2] ?? '') }}</em></span><small>{{ __('site.awards.launch_date') }}</small><span class="sr-only">{{ $a->launched }}</span></li>@endif
        @if ($a->label('vision'))<li class="bf-vision">@if ($logo = $a->image('vision_logo'))<img class="bf-vlogo" src="{{ \App\Support\Media\Src::of($logo) }}" alt="{{ $logo->getCustomProperty('alt', '') }}" width="330" height="235" loading="lazy">@endif<b>{{ $a->label('vision') }}</b><small>{{ $a->label('vision_text') }}</small></li>@endif
        @if ($pins)<li class="bf-where"><x-site.award.oman-map :pins="$pins" :label="__('site.awards.map')" /><b>{{ $a->label('where') }}</b><small>{{ $a->label('where_text') }}</small></li>@endif
      </ul>
      @if (($path = $a->factsIn('path'))->isNotEmpty())
        <h3 class="bf-path-h">{{ $a->label('path_title') }}</h3>
        <ol class="bf-path">@foreach ($path as $i => $p)<li style="--i:{{ $i }}"><span class="bf-step">{{ $d($i + 1) }}</span><div><b>{{ $p->label }}</b><small>{{ $p->value }}</small></div></li>@endforeach</ol>
      @endif
      @if ($a->firsts)<p class="bf-first"><b>{{ $a->label('firsts_label') }}</b>{{ $a->firsts }}</p>@endif
    </div>
  </section>

  @if ($who->isNotEmpty())
    @php $step = 360 / $who->count(); @endphp
    <section class="c-sec" id="c-who" aria-labelledby="c-who-h">{!! $head(++$n, __('site.awards.who'), 'c-who-h') !!}
      <div class="bf-radial" data-radial>
        <svg class="bf-arrows" viewBox="0 0 360 360" aria-hidden="true"><defs><marker id="bfa" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0 10 5 0 10Z" fill="#B8893E"/></marker></defs>
          @foreach ($who as $i => $w)@php $ang = deg2rad(-90 + $i * $step); @endphp<line pathLength="1" style="--i:{{ $i }}" x1="{{ number_format(180 + 60 * cos($ang), 1, '.', '') }}" y1="{{ number_format(180 + 60 * sin($ang), 1, '.', '') }}" x2="{{ number_format(180 + 118 * cos($ang), 1, '.', '') }}" y2="{{ number_format(180 + 118 * sin($ang), 1, '.', '') }}" marker-end="url(#bfa)"/>@endforeach</svg>
        @if ($mark)<span class="bf-core"><img src="{{ \App\Support\Media\Src::of($mark) }}" alt="{{ $a->name }}"></span>@endif
        @foreach ($who as $i => $w)
          @php $ang = deg2rad(-90 + $i * $step); @endphp
          <div class="bf-node" style="--i:{{ $i }};left:{{ number_format(50 + 142 / 3.6 * cos($ang), 2, '.', '') }}%;top:{{ number_format(50 + 142 / 3.6 * sin($ang), 2, '.', '') }}%"><i><x-site.award.icon :name="$w->icon ?: 'flag'" /></i><b>{{ $w->label }}</b><small title="{{ $w->value }}">{{ $w->short ?: $w->value }}</small></div>
        @endforeach
      </div>
    </section>
  @endif

  @if ($letter)
    <section class="c-word" id="c-word" aria-labelledby="c-word-h">
      <div class="w-stage">@if ($photo = \App\Support\Media\Src::of($letter->image('photo')))<svg class="w-img" viewBox="0 0 531 689" role="img" aria-label="{{ trim($letter->title.' '.$letter->name) }}"><defs><filter id="bf-portrait-edge" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB"><feMorphology in="SourceAlpha" operator="erode" radius="1.2" result="clean-alpha"/><feGaussianBlur in="clean-alpha" stdDeviation="0.35" result="soft-alpha"/><feComposite in="SourceGraphic" in2="soft-alpha" operator="in"/></filter></defs><image href="{{ $photo }}" width="531" height="689" filter="url(#bf-portrait-edge)"/></svg>@endif</div><span class="w-line" aria-hidden="true"></span>
      <div class="w-copy"><span class="w-kicker" id="c-word-h">{{ __('site.awards.word_highness') }}</span><b class="w-name gold-t">{{ $letter->name }}</b><small class="w-role">{{ collect([$letter->title, $letter->role])->filter()->implode(' · ') }}</small>
        @if ($letter->quote)<blockquote class="w-quote"><p>{{ $letter->quote }}</p></blockquote>@endif
        <details class="w-full"><summary><span class="o">{{ __('site.awards.read_word') }}</span><span class="c">{{ __('site.awards.hide') }}</span></summary><div>
          @foreach (preg_split('/\R\s*\R/u', (string) $letter->body) as $p)<p>{{ $p }}</p>@endforeach
          @if ($letter->source_text)<p class="w-src">{{ __('site.awards.source') }} @if ($letter->source_url)<a href="{{ $letter->source_url }}" target="_blank" rel="noopener">{{ $letter->source_text }}</a>@else{{ $letter->source_text }}@endif</p>@endif
        </div></details></div>
    </section>
  @endif

  <section class="c-sec" id="c-cycles" aria-labelledby="c-cyc-h">{!! $head(++$n, __('site.awards.cycles'), 'c-cyc-h') !!}
    @foreach ($editions as $c)
      @php
          $top = $c->winners->filter(fn ($w) => $w->rank)->sortBy('rank');
          $extra = $c->winners->filter(fn ($w) => ! $w->rank);
          $gallery = $c->getMedia('gallery');
      @endphp
      <article class="bf-cy">
        <div class="bf-cy-cover">@if ($gallery->isNotEmpty())<img src="{{ $url($gallery->first()) }}" alt="" loading="lazy">@else<span class="bf-cy-pat" aria-hidden="true"></span>@endif
          <span class="bf-cy-year">{{ $d($c->year) }}</span><div class="bf-cy-t"><small>{{ $c->label() }}</small><h3>{{ $c->title }}</h3>@if ($c->place)<p><x-site.award.icon name="pin" />{{ $c->place }}</p>@endif</div></div>
        @if ($c->brief)<p class="bf-cy-brief">{{ $c->brief }}</p>@endif
        <div class="bf-cy-nums"><div><b>{{ $d($c->entries_count) }}</b><span>{{ __('site.awards.participations') }}</span></div><div><b>{{ $d($c->finals_count) }}</b><span>{{ __('site.awards.in_final') }}</span></div><div><b>{{ $d($c->area) }}</b><span>{{ __('site.awards.site_area') }}</span></div></div>
        <div class="bf-cy-meta">@if ($c->jury)<span><i>{{ __('site.awards.jury') }}</i>{{ $c->jury }}</span>@endif @if ($c->exec)<span><i>{{ __('site.awards.exec') }}</i>{{ $c->exec }}</span>@endif @if ($c->ceremony)<span><i>{{ __('site.awards.ceremony') }}</i>{{ $c->ceremony }}</span>@endif</div>
        @foreach ([[$top, ''], [null, 'gallery'], [$extra, ' bf-extra']] as [$list, $class])
          @if ($class === 'gallery')
            <h4 class="bf-gal-h">{{ __('site.awards.gallery') }}</h4>
            @if ($gallery->isNotEmpty())<div class="bf-gal">@foreach ($gallery as $m)<figure><img src="{{ $url($m) }}" alt="{{ $caption($m) }}" loading="lazy"><figcaption>{{ $caption($m) }}@if ($m->getCustomProperty('credit'))<span>{{ __('site.awards.photo_credit', ['credit' => $m->getCustomProperty('credit')]) }}</span>@endif</figcaption></figure>@endforeach</div>@endif
          @elseif ($list->isNotEmpty())
            <ol class="bf-wins{{ $class }}">
              @foreach ($list as $w)
                @php $designs = $w->getMedia('designs'); @endphp
                <li class="bf-w r{{ $w->rank }}"><span class="bf-medal">{{ $w->rank ? $d($w->rank) : '✦' }}</span><small>{{ $w->award_label ?: ($ranks[$w->rank] ?? '') }}</small><h4>«{{ $w->work }}»</h4>
                  <p class="bf-team">{{ implode(' · ', $w->teamNames() ?: [$w->name]) }}</p>
                  @if ($w->why)<div class="bf-why"><b>{{ __('site.awards.why_won') }}</b><p>{{ $w->why }}</p></div>@endif
                  @if ($designs->isNotEmpty())
                    <div @class(['bf-design', 'is-multi' => $designs->count() > 1])>@foreach ($designs as $m)<figure><img src="{{ $url($m) }}" alt="{{ $caption($m) }}" loading="lazy"><figcaption>{{ $caption($m) }}</figcaption></figure>@endforeach</div>
                    @if ($w->source_url)<p class="bf-credit">{{ __('site.awards.photos') }} <a href="{{ $w->source_url }}" target="_blank" rel="noopener">{{ $w->source_title }}</a></p>@endif
                  @else
                    <p class="bf-noimg"><x-site.award.icon name="pin" />{{ __('site.awards.no_design') }}</p>
                  @endif
                </li>
              @endforeach
            </ol>
          @endif
        @endforeach
      </article>
    @endforeach
  </section>

  @if ($features->isNotEmpty())
    <section class="c-sec c-more" id="c-more" aria-labelledby="c-more-h">{!! $head(++$n, $a->label('more_title', __('site.awards.jump_more')), 'c-more-h') !!}
      @foreach ($features as $f)
        @php $images = $f->getMedia('images'); @endphp
        <article class="c-card c-{{ $f->key ?: 'feature' }}">@if ($f->chip)<span class="c-chip">{{ $f->chip }}</span>@endif<h3>{{ $f->title }}</h3>@if ($f->subtitle)<small>{{ $f->subtitle }}</small>@endif
          @if ($f->text)<p>{{ $f->text }}</p>@endif
          @if ($f->teamNames())<div class="c-team">@foreach ($f->teamNames() as $name)<span>{{ $name }}</span>@endforeach</div>@endif
          @if ($images->isNotEmpty())<div class="bf-design is-multi c-expo-img">@foreach ($images as $m)<figure><img src="{{ $url($m) }}" alt="{{ $caption($m) }}" loading="lazy"><figcaption>{{ $caption($m) }}@if ($m->getCustomProperty('credit')) · @if ($m->getCustomProperty('credit_url'))<a href="{{ $m->getCustomProperty('credit_url') }}" target="_blank" rel="noopener">{{ $m->getCustomProperty('credit') }}</a>@else{{ $m->getCustomProperty('credit') }}@endif @endif</figcaption></figure>@endforeach</div>@endif
          @if ($f->note)<p class="{{ $f->key === 'expo' ? 'c-note' : 'c-next' }}">{{ $f->note }}</p>@endif
        </article>
      @endforeach
    </section>
  @endif
  @if ($a->sources->isNotEmpty())
    <section class="c-sec c-src"><details><summary>{{ __('site.awards.sources_refs') }}</summary><ul>
      @foreach ($a->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
    </ul></details></section>
  @endif
</x-site.layout>
