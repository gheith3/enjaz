{{-- Design: bilarab-award.html, with the approved 2050 design (تصاميم ٢٠٥٠ / معتمد / صفحة جائزة السيد بلعرب - ملف 2): the idea
     with the winning projects' showcase, the goals, the design journey (five-chapter scene), the participant card, the framed
     word of His Highness, the editions as coloured bands and «ما بعد الدورتين». Motion: js bha-hero-motion, phone-arc-bilarab,
     scroll-cue, award-architecture (counters and reveal) and polish/award-architecture (the 2050 design). --}}
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
    $em = fn ($text) => preg_replace('/\*\*(.+?)\*\*/u', '<em>$1</em>', e((string) $text));
    $launch = preg_split('/\s+/u', trim((string) $a->launched));
    $who = $a->factsIn('who');
    $path = $a->factsIn('path');
    $goals = $a->factsIn('goals');
    $ranks = __('site.awards.arch_ranks');
    $pins = $editions->filter(fn ($e) => $e->map_x !== null)->map(fn ($e) => [$e->map_label, $e->map_x, $e->map_y])->values()->all();
    $caption = fn ($m) => \App\Support\Localized::get($m->getCustomProperty('caption'));
    $url = fn ($m) => \App\Support\Media\Src::of($m);
    $mark = $a->image('mark');
    $markUrl = $mark ? \App\Support\Media\Src::of($mark) : null;
    $n = 0;

    // Each edition's project: its first-place work (or the edition's title), its winners and pictures.
    $projects = $editions->map(function ($c) use ($d) {
        $top = $c->winners->filter(fn ($w) => $w->rank)->sortBy('rank');
        $first = $top->first();

        return (object) [
            'edition' => $c, 'top' => $top, 'extra' => $c->winners->filter(fn ($w) => ! $w->rank),
            'name' => $first?->work ?: $c->title, 'gallery' => $c->getMedia('gallery'),
            'sub' => collect([$c->label().' '.$d($c->year), $c->place])->filter()->implode(' · '),
            'pictures' => collect($first?->getMedia('designs') ?? [])->merge($c->getMedia('gallery'))->unique('file_name')->values(),
        ];
    });
    // The showcase next to «عن الجائزة»: three pictures of each project, taking turns between the projects.
    $slides = collect();
    for ($i = 0; $i < 3; $i++) {
        foreach ($projects as $p) {
            if ($m = $p->pictures->get($i)) {
                $slides->push(['src' => $url($m), 'cap' => $caption($m), 'title' => $p->name, 'sub' => $p->sub, 'pos' => $m->getCustomProperty('focal') ?: '50% 50%']);
            }
        }
    }
    // The design journey: one chapter per step, with a short fact of the award under each.
    $journeyIcons = ['pin', 'pencil', 'search', 'trophy', 'build'];
    $chips = [
        $editions->filter(fn ($e) => $e->map_label)->map(fn ($e) => $e->map_label.' '.$d($e->year))->implode(' · '),
        $editions->sum('entries_count') ? __('site.awards.journey_entries', ['n' => $d($editions->sum('entries_count'))]) : null,
        $editions->max('finals_count') ? __('site.awards.journey_finals', ['n' => $d($editions->max('finals_count'))]) : null,
        $a->label('firsts_label'),
        __('site.awards.journey_end'),
    ];
    $scene = $path->count() === 5;
    // The scene's map: the map component's shapes drawn straight into the scene (a nested <svg> is sized and placed wrongly there).
    $mapInner = $scene && $pins ? preg_replace('~^\s*<svg[^>]*>|</svg>\s*$~u', '', \Illuminate\Support\Facades\Blade::render('<'.'x-site.award.oman-map :pins="$pins" />', ['pins' => $pins])) : null;
    $cards = [[70, 70], [170, 55], [280, 75], [390, 60], [460, 120], [100, 160], [215, 150], [330, 165], [440, 215], [60, 250], [180, 245], [300, 255]];
    $tilt = [-14, 9, -6, 12, -18, 7, -10, 15, -4, 11, -16, 5];
@endphp
<x-site.layout :title="$a->name" :description="\Illuminate\Support\Str::limit((string) $a->about, 160)" :image="$a->imageUrl('hero')"
    styles="award-architecture" body-class="flush" :noindex="! $a->isLive()" class="cx" data-c="2" data-bf
    :crumb="['title' => $a->label('crumb', $a->name), 'sub' => $a->subtitle]">
  <section class="c-hero" aria-labelledby="c-h1">
    @if ($hero = $a->imageUrl('hero'))<img class="bf-hero-img" src="{{ $hero }}" alt="" aria-hidden="true" width="2200" height="1466">@endif
    <div class="h-mash" aria-hidden="true"></div><div class="h-rays" aria-hidden="true"><i></i><i></i><i></i></div>
    <div class="c-hero-in">@if ($markUrl)<img class="c-mark" src="{{ $markUrl }}" alt="" width="311" height="281">@endif
      @if ($a->eyebrow)<span class="c-eyebrow">{{ $a->eyebrow }}</span>@endif
      <h1 id="c-h1"><span class="gold-t bf-shine" data-t="{{ $a->title ?: $a->name }}">{{ $a->title ?: $a->name }}</span>@if ($a->subtitle)<small>{{ $a->subtitle }}</small>@endif</h1>
      @if ($a->motto)<p class="c-motto">«{{ $a->motto }}»</p>@endif
      <dl class="c-stats">@foreach ($stats as [$value, $label])<div><dt>{{ $label }}</dt><dd data-to="{{ $value }}">{{ $d($value) }}</dd></div>@endforeach</dl></div>
  </section>
  <nav class="c-jump" aria-label="{{ __('site.awards.jump') }}"><a href="#c-about">{{ __('site.awards.jump_idea') }}</a>@if ($who->isNotEmpty())<a href="#c-who">{{ __('site.awards.jump_who') }}</a>@endif @if ($letter)<a href="#c-word">{{ __('site.awards.jump_highness') }}</a>@endif<a href="#c-cycles">{{ __('site.awards.jump_cycles') }}</a>@if ($features->isNotEmpty())<a href="#c-more">{{ __('site.awards.jump_more') }}</a>@endif</nav>

  <section class="c-sec" id="c-about" aria-labelledby="c-about-h">{!! $head(++$n, __('site.awards.idea'), 'c-about-h') !!}
    <div class="ax" data-ax>
      <div class="ax-intro">
        <div class="ax-txt">
          <span class="ax-kick">{{ __('site.awards.about_kicker') }}</span>
          @if ($a->label('idea_title'))<h3>{!! $em($a->label('idea_title')) !!}</h3>@endif
          @if ($ideaText = $a->label('idea_text') ?: $a->lead)<p>{!! $bold($ideaText) !!}</p>@endif
          <ul class="ax-badges">
            @if ($a->label('blessing'))<li>@if ($emblem = $a->image('emblem'))<img src="{{ \App\Support\Media\Src::of($emblem) }}" alt="">@endif<span><b>{{ $a->label('blessing') }}</b><small>{{ $a->label('blessing_by') }}</small></span></li>@endif
            @if ($a->launched)<li><span class="ax-cal" aria-hidden="true"><i>{{ $launch[1] ?? '' }}</i><b>{{ $d($launch[0] ?? '') }}</b><em>{{ $d($launch[2] ?? '') }}</em></span><span><b>{{ __('site.awards.launch_date') }}</b><small>{{ $d($a->launched) }}</small></span></li>@endif
          </ul>
        </div>
        @if ($slides->isNotEmpty())
          @php $s0 = $slides->first(); @endphp
          <div class="ax-fig" data-axs aria-label="{{ __('site.awards.showcase') }}">
            <div class="ax-arch">
              <div class="ax-ttl" aria-live="polite"><span class="ax-ttl-in"><small>{{ $s0['sub'] }}</small><b>{{ $s0['title'] }}</b></span></div>
              <div class="ax-stage"><img class="ax-base" src="{{ $s0['src'] }}" alt="{{ trim($s0['title'].': '.$s0['cap'], ': ') }}" style="object-position:{{ $s0['pos'] }}"><span class="ax-scan" aria-hidden="true"></span></div>
              <div class="ax-capbar"><span class="ax-cap">{{ $s0['cap'] }}</span><span class="ax-cnt" aria-hidden="true"><b>{{ $num(1) }}</b> / {{ $num($slides->count()) }}</span></div>
              <ol class="ax-data" hidden>@foreach ($slides as $s)<li data-t="{{ $s['title'] }}" data-s="{{ $s['sub'] }}" data-c="{{ $s['cap'] }}" data-src="{{ $s['src'] }}" data-pos="{{ $s['pos'] }}"></li>@endforeach</ol>
            </div>
            <dl class="ax-stat">@foreach ($stats as [$value, $label])<div><dt>{{ $label }}</dt><dd>{{ $d($value) }}</dd></div>@endforeach</dl>
          </div>
        @endif
      </div>

      @if ($goals->isNotEmpty())
        <div class="ax-block">
          <h4 class="ax-h"><span>{{ __('site.awards.goals') }}</span></h4>
          <ol class="ax-goals">@foreach ($goals as $i => $g)<li style="--i:{{ $i }}"><span class="ax-gi"><x-site.award.icon :name="$g->icon ?: 'star'" /></span><span class="ax-gn" aria-hidden="true">{{ $num($i + 1) }}</span><b>{{ $g->label }}</b><p>{{ $g->value }}</p></li>@endforeach</ol>
        </div>
      @endif

      @if ($path->isNotEmpty())
        <div class="ax-block">
          <h4 class="ax-h"><span>{{ __('site.awards.journey', ['title' => $a->label('path_title', __('site.awards.journey_end'))]) }}</span></h4>
          @if ($scene)
            <div class="jx" data-jx data-a="0" aria-label="{{ __('site.awards.journey_scene', ['n' => $d(5)]) }}">
              <div class="jx-stage">
                <div class="jx-cap" aria-live="polite">
                  <span class="jx-no"><b>{{ $num(1) }}</b><i>/ {{ $num(5) }}</i></span>
                  <h5 class="jx-t">{{ $path[0]->label }}</h5>
                  <p class="jx-d">{{ $path[0]->value }}</p>
                  <span class="jx-chip">{{ $chips[0] }}</span>
                </div>
                <svg class="jx-svg" viewBox="10 0 570 340" aria-hidden="true">
                  <defs>
                    <linearGradient id="jxBronze" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F0C795"/><stop offset=".5" stop-color="#C78559"/><stop offset="1" stop-color="#8A4B2C"/></linearGradient>
                    <radialGradient id="jxSun"><stop offset="0" stop-color="#FFE6A8"/><stop offset=".35" stop-color="#F5B860" stop-opacity=".7"/><stop offset="1" stop-color="#F5B860" stop-opacity="0"/></radialGradient>
                    <clipPath id="jxGround"><rect x="0" y="0" width="800" height="290"/></clipPath>
                  </defs>
                  <circle class="jx-sun" cx="300" cy="200" r="150" fill="url(#jxSun)"/>
                  <line class="jx-gnd" x1="20" y1="290" x2="560" y2="290"/>
                  @if ($mapInner)<g class="jx-map" transform="translate(160 18) scale(2.05)">{!! $mapInner !!}</g>@endif
                  <g class="jx-cards">
                    @foreach ($cards as $i => [$x, $y])
                      <g @class(['jx-card', 'fin' => ! in_array($i, [3, 8], true)]) style="--x0:{{ $i % 2 ? -90 : 890 }}px;--y0:{{ 40 + ($i * 53) % 260 }}px;--r0:{{ $tilt[($i + 5) % 12] * 3 }}deg;--x2:{{ $x }}px;--y2:{{ $y }}px;--r2:{{ $tilt[$i] }}deg;--x3:{{ 88 + ($i % 6) * 68 }}px;--y3:{{ 135 + intdiv($i, 6) * 58 }}px;--i:{{ $i }}"><rect class="pp" x="-26" y="-19" width="52" height="38" rx="3"/><path class="sk" d="M-17 11V0h8v-6h10v6h8v11M-20 11h40M-5 11V5h6v6"/><rect class="fr" x="-29" y="-22" width="58" height="44" rx="5"/><g class="ok"><circle cx="22" cy="-16" r="7"/><path d="M18.5-16l2.5 2.5 4.5-5"/></g></g>
                    @endforeach
                  </g>
                  <g class="jx-lens"><circle r="22"/><path d="M16 16l16 16"/></g>
                  <g class="jx-pod" clip-path="url(#jxGround)">
                    <g class="rays"><path d="M262 160 L200 20 M262 160 L262 0 M262 160 L324 20 M262 160 L150 70 M262 160 L374 70"/></g>
                    <rect class="b2" x="172" y="220" width="58" height="70" rx="3"/><rect class="b1" x="233" y="190" width="58" height="100" rx="3"/><rect class="b3" x="294" y="240" width="58" height="50" rx="3"/>
                    <text x="201" y="262">{{ $d(2) }}</text><text x="262" y="246">{{ $d(1) }}</text><text x="323" y="272">{{ $d(3) }}</text>
                  </g>
                  <g class="jx-medals"><g class="m2"><circle cx="201" cy="196" r="14"/></g><g class="m1"><circle cx="262" cy="164" r="17"/></g><g class="m3"><circle cx="323" cy="217" r="12"/></g></g>
                  <g class="jx-spark"><path d="M150 120l4 8 8 4-8 4-4 8-4-8-8-4 8-4Z"/><path d="M380 90l3 6 6 3-6 3-3 6-3-6-6-3 6-3Z"/><path d="M330 60l2.5 5 5 2.5-5 2.5-2.5 5-2.5-5-5-2.5 5-2.5Z"/></g>
                  <g class="jx-bld" clip-path="url(#jxGround)">
                    <g class="pc p1"><path d="M110 290V170h110a60 60 0 0 1 0 120Z"/><g class="win">@foreach ([196, 214, 232, 250] as $wy)@foreach ([128, 146, 164, 182, 200] as $wx)<rect x="{{ $wx }}" y="{{ $wy }}" width="9" height="7" rx="1"/>@endforeach @endforeach</g></g>
                    <g class="pc p2"><path d="M292 150h22v140l-22-22Z"/></g>
                    <g class="pc p3"><path d="M322 175l24-25v140l-24-25Z"/></g>
                    <g class="pc p4"><path d="M396 188l42 102h-84Z"/></g>
                  </g>
                  <g class="jx-trees"><circle cx="70" cy="272" r="17"/><circle cx="88" cy="279" r="11"/><circle cx="470" cy="270" r="18"/><circle cx="452" cy="279" r="10"/></g>
                </svg>
              </div>
            </div>
          @endif
          <ol class="ax-steps">@foreach ($path as $i => $p)<li style="--i:{{ $i }}" data-chip="{{ $chips[$i] ?? '' }}"><span class="ax-si"><x-site.award.icon :name="$p->icon ?: ($journeyIcons[$i] ?? 'pin')" /><i>{{ $d($i + 1) }}</i></span><b>{{ $p->label }}</b><small>{{ $p->value }}</small></li>@endforeach</ol>
        </div>
      @endif

      <ul class="ax-feats">
        @if ($a->label('vision'))<li><span class="ax-fi">@if ($logo = $a->image('vision_logo'))<img src="{{ \App\Support\Media\Src::of($logo) }}" alt="{{ $logo->getCustomProperty('alt', '') }}" loading="lazy">@endif</span><b>{{ $a->label('vision') }}</b><small>{{ $a->label('vision_text') }}</small></li>@endif
        @if ($pins)<li><span class="ax-fi"><x-site.award.oman-map :pins="$pins" :label="__('site.awards.map')" /></span><b>{{ $a->label('where') }}</b><small>{{ $a->label('where_text') }}</small></li>@endif
        @if ($a->firsts)<li class="ax-nft"><span class="ax-fi"><span class="ax-shield">@if ($markUrl)<img src="{{ $markUrl }}" alt="">@endif</span></span><b>{{ $a->label('firsts_label') }}</b><small>{{ $a->firsts }}</small></li>@endif
      </ul>
    </div>
  </section>

  @if ($who->isNotEmpty())
    <section class="c-sec" id="c-who" aria-labelledby="c-who-h">{!! $head(++$n, __('site.awards.who'), 'c-who-h') !!}
      <div class="px" data-px @if ($markUrl) style="--mark:url('{{ $markUrl }}')" @endif>
        <div class="px-main">
          <div class="px-stamp" role="img" aria-label="{{ __('site.awards.stamp') }} {{ __('site.awards.stamp_sub') }}"><svg viewBox="0 0 120 120" aria-hidden="true"><circle class="o" cx="60" cy="60" r="57"/><circle class="i" cx="60" cy="60" r="52"/><g class="d">@for ($k = 0; $k < 24; $k++)<circle cx="{{ number_format(60 + 47 * cos($k * M_PI / 12), 2, '.', '') }}" cy="{{ number_format(60 + 47 * sin($k * M_PI / 12), 2, '.', '') }}" r="1.6"/>@endfor</g><circle class="i" cx="60" cy="60" r="41"/></svg><span class="px-st-in">@if ($markUrl)<img src="{{ $markUrl }}" alt="">@endif<b>{{ __('site.awards.stamp') }}</b><small>{{ __('site.awards.stamp_sub') }}</small></span></div>
          <span class="px-kick">{{ __('site.awards.card_kicker') }}</span>
          <h3>{{ __('site.awards.card_title') }}</h3>
          <p class="px-lead">{{ __('site.awards.card_lead') }}</p>
          <ul class="px-list">@foreach ($who as $i => $w)<li style="--i:{{ $i }}"><span class="px-ic"><x-site.award.icon :name="$w->icon ?: 'flag'" /></span><span class="px-tx"><b>{{ $w->label }}</b><span>{{ $w->value }}</span></span><span class="px-ok" aria-hidden="true"><svg viewBox="0 0 16 16"><path d="M3.5 8.5l3 3 6-7"/></svg></span></li>@endforeach</ul>
        </div>
        @if ($a->label('who_big') || $a->label('who_team') || $a->label('who_open'))
          <div class="px-stub" aria-label="{{ __('site.awards.card_summary') }}">
            @if ($markUrl)<img class="px-mark" src="{{ $markUrl }}" alt="">@endif
            @if ($a->label('who_big'))<div class="px-big"><b>{{ $a->label('who_big') }}</b><span>{{ $a->label('who_big_unit') }}<br>{{ $a->label('who_big_text') }}</span></div>@endif
            @if ($a->label('who_team'))<div class="px-team"><b>{{ $a->label('who_team') }}</b><span>{{ $a->label('who_team_text') }}</span></div>@endif
            @if ($a->label('who_open'))<p class="px-open">{{ $a->label('who_open') }}</p>@endif
            <span class="px-code" aria-hidden="true"></span>
          </div>
        @endif
      </div>
    </section>
  @endif

  @if ($letter)
    <section class="c-word" id="c-word" aria-labelledby="c-word-h" @if ($markUrl) style="--mark:url('{{ $markUrl }}')" @endif>
      <div class="w-stage">@if ($photo = \App\Support\Media\Src::of($letter->image('photo')))<svg class="w-img" viewBox="0 0 531 689" role="img" aria-label="{{ trim($letter->title.' '.$letter->name) }}"><defs><filter id="bf-portrait-edge" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB"><feMorphology in="SourceAlpha" operator="erode" radius="1.2" result="clean-alpha"/><feGaussianBlur in="clean-alpha" stdDeviation="0.35" result="soft-alpha"/><feComposite in="SourceGraphic" in2="soft-alpha" operator="in"/></filter></defs><image href="{{ $photo }}" width="531" height="689" filter="url(#bf-portrait-edge)"/></svg>@endif</div><span class="w-line" aria-hidden="true"></span>
      <div class="w-copy"><span class="w-kicker" id="c-word-h">{{ __('site.awards.word_highness') }}</span><b class="w-name gold-t">{{ $letter->name }}</b>
        @if ($letter->quote)<blockquote class="w-quote"><p>{{ $letter->quote }}</p></blockquote>@endif
        <details class="w-full"><summary><span class="o">{{ __('site.awards.read_word') }}</span><span class="c">{{ __('site.awards.hide') }}</span></summary><div>
          @foreach (preg_split('/\R\s*\R/u', (string) $letter->body) as $p)<p>{{ $p }}</p>@endforeach
          @if ($letter->source_text)<p class="w-src">{{ __('site.awards.source') }} @if ($letter->source_url)<a href="{{ $letter->source_url }}" target="_blank" rel="noopener">{{ $letter->source_text }}</a>@else{{ $letter->source_text }}@endif</p>@endif
        </div></details></div>
    </section>
  @endif

  <section class="c-sec" id="c-cycles" aria-labelledby="c-cyc-h">{!! $head(++$n, __('site.awards.cycles'), 'c-cyc-h') !!}
    <div class="cz" data-cz data-labels="{{ json_encode(['count' => __('site.awards.gallery_count'), 'swipe' => __('site.awards.gallery_swipe'), 'end' => __('site.awards.gallery_end'), 'prev' => __('site.awards.gallery_prev'), 'next' => __('site.awards.gallery_next'), 'viewer' => __('site.awards.viewer'), 'close' => __('site.awards.viewer_close'), 'vprev' => __('site.awards.viewer_prev'), 'vnext' => __('site.awards.viewer_next'), 'credit' => __('site.awards.photo_credit')], JSON_UNESCAPED_UNICODE) }}">
      @foreach ($projects as $ci => $p)
        @php $c = $p->edition; $band = $p->gallery->first() ?? $p->pictures->first(); @endphp
        <div class="cz-panel on" id="cy{{ $ci + 1 }}" role="region" aria-label="{{ $c->label() }}">
          <header @class(['cz-band', 'c'.(($ci % 2) + 1)]) @if ($band) style="--bg:url('{{ $url($band) }}')" @endif>
            <span class="cz-band-sh" aria-hidden="true"></span><span class="cz-band-fr" aria-hidden="true"><i></i><i></i><i></i><i></i></span><span class="cz-band-n" aria-hidden="true">{{ $d($c->number ?: $ci + 1) }}</span>
            <div class="cz-band-t"><span class="cz-band-k">{{ $c->label() }} · {{ $d($c->year) }}</span><h3>{{ $p->name }}</h3>@if ($c->place)<p>{{ $c->place }}</p>@endif</div>
            <ul class="cz-band-s">
              @if ($c->entries_count)<li><b>{{ $d($c->entries_count) }}</b><span>{{ __('site.awards.participations') }}</span></li>@endif
              @if ($c->finals_count)<li><b>{{ $d($c->finals_count) }}</b><span>{{ __('site.awards.in_final') }}</span></li>@endif
              @if ($c->area)<li><b>{{ $d($c->area) }}</b><span>{{ __('site.awards.site_area') }}</span></li>@endif
            </ul>
          </header>
          @if ($c->brief || $c->jury || $c->exec || $c->ceremony || $c->title !== $p->name)
            <div class="cz-head"><div class="cz-info">
              <div>@if ($c->title && $c->title !== $p->name)<p class="cz-title">{{ $c->title }}</p>@endif @if ($c->brief)<p class="cz-brief">{{ $c->brief }}</p>@endif</div>
              <dl class="cz-meta">@foreach ([['jury', $c->jury], ['exec', $c->exec], ['ceremony', $c->ceremony]] as [$key, $value])@if ($value)<div><dt>{{ __('site.awards.'.$key) }}</dt><dd>{{ $value }}</dd></div>@endif @endforeach</dl>
            </div></div>
          @endif
          @foreach ([[$p->top, 'cz-wins'], [$p->extra, 'cz-wins cz-extra']] as [$list, $class])
            @if ($list->isNotEmpty())
              @if ($loop->first)<h4 class="cz-h"><span>{{ __('site.awards.winners') }}</span></h4>@endif
              <ol class="{{ $class }}">
                @foreach ($list as $w)
                  @php $designs = $w->getMedia('designs'); $credit = $w->source_title; @endphp
                  <li class="cz-w r{{ $w->rank ?: 0 }}">
                    @if ($designs->isNotEmpty())
                      <div class="cz-media"><button type="button" class="cz-img" data-lb="{{ $url($designs->first()) }}" data-cap="{{ $caption($designs->first()) }}" data-cr="{{ $credit }}" aria-label="{{ __('site.awards.zoom') }}"><img src="{{ $url($designs->first()) }}" alt="{{ $caption($designs->first()) }}" loading="lazy"></button>
                        @if ($designs->count() > 1)<div class="cz-thumbs">@foreach ($designs as $m)<button type="button" @class(['on' => $loop->first]) data-src="{{ $url($m) }}" data-cap="{{ $caption($m) }}" aria-label="{{ $caption($m) }}"><img src="{{ $url($m) }}" alt="" loading="lazy"></button>@endforeach</div>@endif
                      </div>
                    @else
                      <div class="cz-media cz-none">@if ($markUrl)<img src="{{ $markUrl }}" alt="">@endif<span>{{ __('site.awards.design_unpublished') }}</span></div>
                    @endif
                    <div class="cz-body"><span class="cz-medal"><b>{{ $w->rank ? $d($w->rank) : '✦' }}</b>{{ $w->award_label ?: ($ranks[$w->rank] ?? '') }}</span><h4>«{{ $w->work }}»</h4>
                      <ul class="cz-team">@foreach ($w->teamNames() ?: [$w->name] as $name)<li>{{ $name }}</li>@endforeach</ul>
                      @if ($w->why)<div class="cz-why"><b>{{ __('site.awards.why_won') }}</b><p>{{ $w->why }}</p></div><button type="button" class="cz-more" aria-expanded="false"><span class="o">{{ __('site.awards.read_more') }}</span><span class="c">{{ __('site.awards.read_less') }}</span></button>@endif
                      @if ($designs->isNotEmpty() && $w->source_url)<p class="cz-credit">{{ __('site.awards.photos') }} <a href="{{ $w->source_url }}" target="_blank" rel="noopener">{{ $w->source_title }}</a></p>@endif
                    </div>
                  </li>
                @endforeach
              </ol>
            @endif
          @endforeach
          @if ($p->gallery->isNotEmpty())
            <h4 class="cz-h"><span>{{ __('site.awards.gallery') }}</span><small>{{ __('site.awards.gallery_hint') }}</small></h4>
            <div class="cz-gal">@foreach ($p->gallery as $m)<button type="button" class="cz-img" data-lb="{{ $url($m) }}" data-cap="{{ $caption($m) }}" data-cr="{{ $m->getCustomProperty('credit') }}" aria-label="{{ $caption($m) ?: __('site.awards.zoom') }}"><img src="{{ $url($m) }}" alt="{{ $caption($m) }}" loading="lazy"></button>@endforeach</div>
          @endif
        </div>
      @endforeach
    </div>
  </section>

  @if ($features->isNotEmpty())
    @php $expoFacts = $a->factsIn('expo'); $pillars = $a->factsIn('initiative'); $chapter = 0; @endphp
    <section class="c-sec c-more" id="c-more" aria-labelledby="c-more-h">{!! $head(++$n, $a->label('more_title', __('site.awards.jump_more')), 'c-more-h') !!}
      <div class="mx" data-mx>
        @if ($a->label('more_lead'))<p class="mx-lead">{!! $bold($a->label('more_lead')) !!}</p>@endif
        <ol class="mx-tl" aria-label="{{ __('site.awards.timeline') }}">
          @foreach ($projects as $p)<li style="--i:{{ $loop->index }}"><b>{{ $d($p->edition->year) }}</b><span>{{ $p->edition->label() }}</span><small>{{ $p->name }}</small></li>@endforeach
          @foreach ($features as $f)<li class="hl" style="--i:{{ $projects->count() + $loop->index }}"><b>{{ $f->chip }}</b><span>{{ $f->title }}</span>@if ($f->subtitle)<small>{{ $f->subtitle }}</small>@endif</li>@endforeach
        </ol>
        @foreach ($features as $f)
          @php
              $images = $f->getMedia('images');
              $chapter++;
              $credit = fn ($m) => $m->getCustomProperty('credit');
          @endphp
          @if ($f->key === 'ihya')
            @php
                preg_match('/^([\d٠-٩]+(?:\s*[–-]\s*[\d٠-٩]+)?)\s+(\S+)\s+([\d٠-٩]{4})$/u', trim((string) $f->chip), $when);
                $venue = array_map('trim', preg_split('/\s+[–-]\s+/u', (string) $f->subtitle, 2));
            @endphp
            <article class="mx-ihya">
              <div class="mx-ih-side">
                <span class="mx-chip">{{ __('site.awards.chapter', ['n' => $d($chapter)]) }}</span>
                <h3>{{ $f->title }}</h3>
                <div class="mx-when">
                  @if ($when)<span class="mx-cal" aria-hidden="true"><i>{{ $when[2] }}</i><b>{{ $d($when[1]) }}</b><em>{{ $d($when[3]) }}</em></span>@else<span class="mx-chip">{{ $f->chip }}</span>@endif
                  @if ($f->subtitle)<span><b>{{ $venue[0] }}</b>@if (isset($venue[1]))<small>{{ $venue[1] }}</small>@endif</span>@endif
                </div>
                @if ($f->text)<p>{{ $f->text }}</p>@endif
                @if ($f->note)<p>{{ $f->note }}</p>@endif
                <span class="mx-ih-blocks" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
              </div>
              @if ($pillars->isNotEmpty())<ul class="mx-ih-grid">@foreach ($pillars as $i => $pl)<li style="--i:{{ $i }}"><span class="mx-ic"><x-site.award.icon :name="$pl->icon ?: 'star'" /></span><b>{{ $pl->label }}</b><small>{{ $pl->value }}</small></li>@endforeach</ul>@endif
            </article>
          @else
            <article class="mx-expo">
              @if ($images->isNotEmpty())
                <div class="mx-ex-media">
                  <button type="button" class="cz-img" data-lb="{{ $url($images->first()) }}" data-cap="{{ $caption($images->first()) }}" data-cr="{{ $credit($images->first()) }}" aria-label="{{ __('site.awards.zoom') }}"><img src="{{ $url($images->first()) }}" alt="{{ $caption($images->first()) }}" loading="lazy"></button>
                  @if ($second = $images->get(1))<div class="mx-ex-inset"><button type="button" class="cz-img" data-lb="{{ $url($second) }}" data-cap="{{ $caption($second) }}" data-cr="{{ $credit($second) }}" aria-label="{{ __('site.awards.zoom') }}"><img src="{{ $url($second) }}" alt="{{ $caption($second) }}" loading="lazy"></button></div>@endif
                  @if ($f->key === 'expo' && $a->label('expo_tag'))<span class="mx-ex-tag">{{ $a->label('expo_tag') }}</span>@endif
                </div>
              @endif
              <div class="mx-ex-body">
                <span class="mx-chip">{{ collect([$f->chip, __('site.awards.chapter', ['n' => $d($chapter)])])->filter()->implode(' · ') }}</span>
                <h3>{{ $f->title }}</h3>
                @if ($f->text)<p>{{ $f->text }}</p>@endif
                @if ($f->key === 'expo' && $expoFacts->isNotEmpty())<ol class="mx-route">@foreach ($expoFacts as $r)<li><b>{{ $r->label }}</b><span>{{ $r->value }}</span></li>@endforeach</ol>@endif
                @if ($f->teamNames())<div class="mx-team"><span class="mx-team-h">{{ __('site.awards.expo_team') }}</span><ul>@foreach ($f->teamNames() as $name)<li>{{ $name }}</li>@endforeach</ul></div>@endif
                @if ($f->note)<p class="mx-note">@if ($f->key === 'expo' && $a->label('expo_note_title'))<b>✦ {{ $a->label('expo_note_title') }}</b>@endif{{ $f->note }}</p>@endif
                @if ($images->contains(fn ($m) => $credit($m)))
                  <p class="mx-credit">{{ __('site.awards.photos') }} @foreach ($images->filter(fn ($m) => $credit($m)) as $m)@if (! $loop->first) · @endif @if ($m->getCustomProperty('credit_url'))<a href="{{ $m->getCustomProperty('credit_url') }}" target="_blank" rel="noopener">{{ $credit($m) }}</a>@else{{ $credit($m) }}@endif @endforeach</p>
                @endif
              </div>
            </article>
          @endif
        @endforeach
      </div>
    </section>
  @endif
  @if ($a->sources->isNotEmpty())
    <section class="c-sec c-src"><details><summary>{{ __('site.awards.sources_refs') }}</summary><ul>
      @foreach ($a->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
    </ul></details></section>
  @endif
</x-site.layout>
