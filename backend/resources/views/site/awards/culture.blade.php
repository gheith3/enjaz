{{-- Design: award.html (js/award.js). Everything is rendered here; js award-culture only filters the medals,
     fills the winner preview (desktop) and the bottom sheet (phone) from the winners JSON below. --}}
@php
    $a = $award;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n ?? '');
    $branches = $a->categoriesOf('branch');
    $prizes = $a->categoriesOf('prize');
    $done = $a->editions->filter(fn ($e) => ! $e->is_current && $e->type !== 'pause');
    $current = $a->editions->firstWhere('is_current', true);
    $timeline = $done->sortByDesc('sort_order')->values();
    $all = $timeline->flatMap(fn ($e) => $e->winners->map(fn ($w) => [$w, $e]))->values();
    $arabCount = $all->filter(fn ($x) => $x[1]->type === 'arab')->count();
    $firstYear = $done->min(fn ($e) => (int) $e->year);
    $ord = __('site.awards.ordinals_f');
    $photo = fn ($w) => $w->imageUrl('photo', 'card');
    $index = $all->mapWithKeys(fn ($x, $i) => [$x[0]->id => $i]);
    $letter = $a->letter?->is_published ? $a->letter : null;
    $page = \App\Models\Page::forKey('awards');
@endphp
<x-site.layout :title="$a->name" :description="\Illuminate\Support\Str::limit((string) $a->about, 160)" :image="$a->imageUrl('logo')"
    styles="award-culture" body-class="flush" :noindex="! $a->isLive()" class="aw-page" data-award
    :crumb="['title' => $page->title, 'url' => route('awards.index'), 'sub' => $a->label('field_label')]">
  <section class="aw-hero" aria-labelledby="aw-h1">
    <div class="aw-signature">
      <span class="aw-emblem" role="img" aria-label="{{ $a->name }}"><span class="aw-gold"></span></span>
      <span class="aw-floor" aria-hidden="true"></span>
    </div>
    @if ($a->eyebrow)<span class="aw-eyebrow">{{ $a->eyebrow }}</span>@endif
    <h1 id="aw-h1">{{ $a->title ?: $a->name }}</h1>
    <p class="aw-decree">@if ($a->decree){{ __('site.awards.decree') }} <b>{{ $a->decree }}</b><i></i>@endif @if ($a->founded_year){{ __('site.awards.since') }} <b>{{ $a->founded_year }}</b>@endif</p>
    <div class="aw-stats">
      @foreach ([[$d($all->count()), __('site.awards.stat_crowns')], [$d($done->count()), __('site.awards.stat_editions', ['year' => $firstYear])], [$d($arabCount), __('site.awards.stat_arab')], [$d($branches->count()), __('site.awards.stat_branches')]] as [$n, $label])
        <span><b>{{ $n }}</b><small>{{ $label }}</small></span>
      @endforeach
    </div>
    <nav class="aw-jump" aria-label="{{ __('site.awards.jump') }}"><a href="#aw-about">{{ __('site.awards.jump_about') }}</a>@if ($letter)<a href="#aw-word">{{ __('site.awards.jump_word') }}</a>@endif<a href="#aw-winners">{{ __('site.awards.jump_winners') }}</a><a href="#aw-how">{{ __('site.awards.jump_how') }}</a>@if ($current)<a href="#aw-now">{{ __('site.awards.jump_now') }}</a>@endif</nav>
  </section>
  <div class="wrap">
    <section class="aw-sec" id="aw-about" aria-labelledby="aw-about-h">
      <h2 class="aw-h" id="aw-about-h">{{ __('site.awards.about') }}</h2>
      <p class="aw-lead">{{ $a->about }}</p>
      <ol class="aw-goals">@foreach ($a->factsIn('goals') as $i => $goal)<li><b>{{ $d($i + 1) }}</b>{{ $goal->value }}</li>@endforeach</ol>
      @if ($title = $a->label('pillars_title'))<h3 class="aw-sub">{{ $title }}</h3>@endif
      <div class="aw-pillars">
        @foreach ($branches as $key => $branch)
          <button type="button" class="aw-pil b-{{ $key }}" data-jump-branch="{{ $key }}"><span class="aw-pil-ic"><x-site.award.icon :name="$key" /></span>
            <b>{{ $branch->name }}</b><small>{{ $branch->hint }}</small><em><strong>{{ $d($all->filter(fn ($x) => $x[0]->award_category_id === $branch->id)->count()) }}</strong> {{ __('site.awards.branch_winners') }}</em></button>
        @endforeach
      </div>
    </section>

    @if ($letter)
      <section class="aw-sec aw-word" id="aw-word" aria-labelledby="aw-word-h">
        <h2 class="aw-h" id="aw-word-h">{{ $a->label('word_title', __('site.awards.word')) }}</h2>
        <div><x-site.organization.word-d5 :letter="$letter" variant="leader" /></div>
      </section>
    @endif

    <section class="aw-sec" id="aw-winners" aria-labelledby="aw-win-h">
      <div class="aw-win-head">@if ($eyebrow = $a->label('winners_eyebrow'))<span class="aw-eyebrow">{{ $eyebrow }}</span>@endif
        <h2 id="aw-win-h">{{ __('site.awards.crowned') }} <small><b data-aw-count>{{ $d($all->count()) }}</b> {{ __('site.awards.crowns') }}</small></h2>
        @if ($note = $a->label('winners_note'))<p>{{ $note }}</p>@endif</div>
      <div class="aw-filters" role="group" aria-label="{{ __('site.awards.filter_branch') }}">
        <button type="button" data-aw-f="" aria-pressed="true">{{ __('site.awards.all') }}</button>
        @foreach ($branches as $key => $branch)<button type="button" class="b-{{ $key }}" data-aw-f="{{ $key }}" aria-pressed="false"><x-site.award.icon :name="$key" />{{ $branch->name }}</button>@endforeach
      </div>
      <div class="aw-winners-layout"><ol class="aw-tl" data-aw-tl>
        @foreach ($timeline->filter(fn ($e) => $e->winners->isNotEmpty()) as $e)
          <li @class(['aw-ed', 'is-arab' => $e->type === 'arab'])><div class="aw-knot"><b>{{ $e->year }}</b></div>
            <div class="aw-ed-h"><strong>{{ $e->label() }}</strong><span class="aw-type">{{ __('site.awards.'.($e->type === 'arab' ? 'arab' : 'oman').'_edition') }}</span>@if ($e->note)<small>{{ $e->note }}</small>@endif</div>
            <ul class="aw-ms">
              @foreach ($e->winners as $w)
                @php $key = $w->category?->key; @endphp
                <li @class(['aw-m', 'b-'.$key, 'is-arab' => $e->type === 'arab']) data-branch="{{ $key }}"><button type="button" data-w="{{ $index[$w->id] }}" aria-label="{{ $w->name }}">
                  <span class="aw-rib" aria-hidden="true"></span>
                  <span class="aw-disc">@if ($src = $photo($w))<img src="{{ $src }}" alt="" loading="lazy" @if ($w->is_group) class="is-group" @endif>@endif</span>
                  <b>{{ $w->name }}</b><small>{{ $w->field }}</small><i class="aw-chip">{{ $w->category?->name }}</i></button></li>
              @endforeach
            </ul></li>
        @endforeach
      </ol><aside class="aw-winner-preview" data-aw-preview aria-label="{{ __('site.awards.preview') }}" aria-live="polite"></aside></div>
    </section>

    <section class="aw-sec" id="aw-how" aria-labelledby="aw-how-h">
      <h2 class="aw-h" id="aw-how-h">{{ __('site.awards.how') }}</h2>
      @if ($a->lead)<p class="aw-lead">{{ $a->lead }}</p>@endif
      <ol class="aw-alt">
        @foreach ($a->editions->sortBy('sort_order') as $e)
          <li @class(['t-'.($e->type ?: 'oman'), 'is-now' => $e->is_current])><b>{{ $e->year }}</b><small>{{ __('site.awards.type_'.($e->type ?: 'oman')) }}</small></li>
        @endforeach
      </ol>
      <div class="aw-prizes">
        @foreach ($prizes as $key => $prize)
          <div class="aw-prize p-{{ $key }}"><span class="aw-seal" aria-hidden="true"><i></i></span><b>{{ $prize->name }}</b><p>{{ $prize->medal }}</p><em>{{ $prize->basis }}</em></div>
        @endforeach
      </div>
    </section>

    @if ($current)
      <section class="aw-sec aw-now" id="aw-now" aria-labelledby="aw-now-h"><h2 class="sr-only" id="aw-now-h">{{ __('site.awards.now') }}</h2><div>
        <div class="aw-now-h"><span class="aw-live"><i></i>{{ __('site.awards.now') }}</span><h3>{{ $current->label() }} · {{ $current->year }}</h3>@if ($current->type)<small>{{ __('site.awards.'.($current->type === 'arab' ? 'arab' : 'oman').'_edition') }}</small>@endif</div>
        <ul>@foreach ($current->open_fields ?? [] as $f)<li class="b-{{ $f['category'] }}"><span class="aw-pil-ic"><x-site.award.icon :name="$f['category']" /></span><small>{{ __('site.awards.branch', ['name' => $branches[$f['category']]->name ?? '']) }}</small><b>{{ \App\Support\Localized::get($f['text'] ?? null) }}</b></li>@endforeach</ul>
        @if ($current->status)<p class="aw-now-s">{{ $current->status }}</p>@endif
      </div></section>
    @endif

    @if ($a->sources->isNotEmpty())
      <section class="aw-sec aw-src" aria-labelledby="aw-src-h"><h2 class="aw-h" id="aw-src-h">{{ __('site.awards.sources') }}</h2><ul>
        @foreach ($a->sources as $s)<li>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
      </ul></section>
    @endif
  </div>
  <div class="aw-sheet" data-aw-sheet hidden><div class="aw-card" role="dialog" aria-modal="true" aria-labelledby="aw-card-h"></div></div>
  @php
      $winners = $all->map(function ($x) use ($ord, $prizes, $photo) {
          [$w, $e] = $x;
          $prize = $prizes[$e->type] ?? null;

          return [
              'name' => $w->name, 'photo' => $photo($w), 'group' => $w->is_group, 'branch' => $w->category?->key, 'branchName' => $w->category?->name,
              'field' => $w->field, 'why' => $w->why, 'work' => $w->work, 'arab' => $e->type === 'arab',
              'edition' => ($ord[$e->number] ?? $e->number).' · '.$e->year.($prize ? ' ('.$prize->name.')' : ''),
              'medal' => $prize?->medal, 'basis' => $prize?->basis, 'profile' => $w->achiever?->isLive() ? $w->achiever->url() : null,
          ];
      })->values();
      $labels = collect(['preview_label', 'why', 'work', 'edition', 'honor', 'notable', 'notable_text', 'profile', 'close', 'no_winners'])
          ->mapWithKeys(fn ($k) => [$k => __('site.awards.'.$k)])->put('branch', __('site.awards.branch', ['name' => '%s']));
  @endphp
  <script>window.INJAZ_AWARD_WINNERS = {!! json_encode(['winners' => $winners, 'labels' => $labels], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
</x-site.layout>
