{{-- Design: lab-item.html. Inventions: «ملف الابتكار» (css lab-dossier, js sections/lab-dossier) — a lab dossier on the
     section's blueprint: the cover in a specimen frame (corner marks, ruler, scan line, full view on click), a metal medal,
     the story beside a sticky specification sheet, numbered references and specimen cards for the other inventions.
     Publications: «الورقة البحثية» (css lab-paper, js sections/lab-paper) — see the branch below. --}}
@php
    $x = $item;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $inv = $x->isInvention();
    $segment = $x->segment();
    $cover = $x->image('cover');
    $minutes = $x->readMinutes();
    $read = match (true) { $minutes === 1 => __('site.lab.read_1'), $minutes === 2 => __('site.lab.read_2'), default => __('site.lab.read_n', ['n' => $d($minutes)]) };
    // A fact value listing several things («نظارة ذكية، تطبيق، ...») becomes chips.
    $chips = function (string $value): ?array {
        $parts = array_values(array_filter(array_map('trim', preg_split('/[،,]/u', $value))));

        return count($parts) > 1 && count($parts) <= 8 && collect($parts)->every(fn ($p) => mb_strlen($p) <= 32) ? $parts : null;
    };
@endphp
<x-site.layout :title="$x->title" :description="$inv ? $x->lead : $x->summary" :image="$x->imageUrl('cover')" styles="lab-item" og-type="article"
    :noindex="! $x->isLive()"
    :crumb="['title' => $page->label('crumb', __('site.lab.crumb')), 'url' => route('lab.index'), 'sub' => __('site.lab.'.$segment)]">
@if ($inv)
  @php
      $person = $x->achiever?->isLive() ? $x->achiever : null;
      $avatar = $person?->headerAvatarUrl();
      $org = $x->organization;
      $orgLogo = $org?->logoUrl();
  @endphp
  <article class="wrap ld" data-lab-item data-lab-dossier>
    <header class="ld-hero">
      <svg class="ld-mol" viewBox="0 0 220 120" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="1.4">
        <path pathLength="1" d="M20 60 L38 50 L56 60 L56 80 L38 90 L20 80 Z M56 60 L74 50 L92 60 L92 80 L74 90 L56 80 M92 60 L112 48 L130 58 M74 50 L74 30"/>
        <circle cx="112" cy="48" r="4"/><circle cx="130" cy="58" r="3"/><circle cx="74" cy="30" r="3"/></g></svg>
      <div class="ld-hero-t">
        <span class="ld-kicker"><i></i>{{ __('site.lab.dossier') }}@if ($x->field) · {{ $x->field }}@endif</span>
        <h1>{{ $x->title }}</h1>
        <div class="ld-who">
          <span class="ld-av" aria-hidden="true">@if ($avatar)<img src="{{ $avatar }}" alt="">@else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>@endif</span>
          <span class="ld-who-t"><small>{{ __('site.lab.inventor') }}</small>@if ($person)<a href="{{ $person->url() }}">{{ $x->who }}</a>@else<b>{{ $x->who }}</b>@endif</span>
          @if ($orgName = $x->orgName())
            <span class="ld-org">@if ($orgLogo)<img src="{{ $orgLogo }}" alt="">@endif
              @if ($org?->isLive())<a href="{{ $org->url() }}">{{ $orgName }}</a>@else<span>{{ $orgName }}</span>@endif</span>
          @endif
        </div>
        <ul class="ld-meta">
          @if ($x->event_label)<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"/></svg>{{ $x->event_label }}</li>@endif
          @if ($x->place)<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $x->place }}</li>@endif
          @if ($date = $x->dateLabel())<li><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/></svg>{{ $date }}</li>@endif
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>{{ $read }}</li>
        </ul>
      </div>
      <div class="ld-media">
        @if ($cover)
          <button type="button" class="ld-frame" data-ld-zoom aria-label="{{ __('site.lab.view_photo') }}">
            <img src="{{ $x->imageUrl('cover') }}" alt="{{ \App\Models\LabItem::alt($cover) }}" style="object-position: {{ \App\Models\LabItem::focal($cover) }}">
            <i class="ld-c tl"></i><i class="ld-c tr"></i><i class="ld-c bl"></i><i class="ld-c br"></i><i class="ld-scan"></i>
            <span class="ld-zoom-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.5-4.5M11 8v6M8 11h6"/></svg></span>
          </button>
        @endif
        @if ($x->medal)
          <span class="ld-medal m-{{ $x->medal }}"><span class="ld-medal-rib" aria-hidden="true"></span><span class="ld-medal-disc"><b>{{ $x->medalLabel() }}</b>@if ($year = $x->year())<small>{{ $year }}</small>@endif</span></span>
        @endif
        @if ($x->credit_text)
          <p class="ld-credit">{{ __('site.lab.photos') }} @if ($x->credit_url)<a href="{{ $x->credit_url }}" target="_blank" rel="noopener">{{ $x->credit_text }}</a>@else{{ $x->credit_text }}@endif</p>
        @endif
      </div>
    </header>

    <div class="ld-body-wrap">
      <div class="ld-main">
        @if ($x->lead)<p class="ld-lead">{{ $x->lead }}</p>@endif
        <div class="ld-body">{!! str((string) $x->body)->sanitizeHtml() !!}</div>
      </div>
      <aside class="ld-side">
        @if ($x->facts->isNotEmpty())
          <section class="ld-spec" aria-labelledby="ld-spec-h">
            <h2 id="ld-spec-h"><span>{{ __('site.lab.facts') }}</span>@if ($x->year())<small dir="ltr">SPEC · {{ $x->year() }}</small>@endif</h2>
            <dl>@foreach ($x->facts as $i => $f)
              <div style="--i:{{ $i }}"><dt>{{ $f->label }}</dt><dd>@if ($list = $chips((string) $f->value))<span class="ld-chips">@foreach ($list as $c)<span>{{ $c }}</span>@endforeach</span>@else{{ $f->value }}@endif</dd></div>
            @endforeach</dl>
          </section>
        @endif
        @if ($x->sources->isNotEmpty())
          <section class="ld-refs" aria-labelledby="ld-refs-h"><h2 id="ld-refs-h">{{ __('site.lab.sources') }}</h2><ol>
            @foreach ($x->sources as $i => $s)<li><span>{{ $d($i + 1) }}</span>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
          </ol></section>
        @endif
      </aside>
    </div>

    @if ($others->isNotEmpty())
      <section class="ld-more" aria-labelledby="ld-more-h">
        <h2 id="ld-more-h">{{ __('site.lab.other_'.$segment) }}</h2>
        <div class="ld-others">
          @foreach ($others as $o)
            <a class="ld-o" href="{{ $o->url() }}">
              <span class="ld-o-img">@if ($img = $o->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif<i class="ld-c tl"></i><i class="ld-c br"></i>
                @if ($o->medal)<span class="ld-o-medal m-{{ $o->medal }}">{{ $o->medalLabel() }}</span>@endif</span>
              <span class="ld-o-t"><small>{{ $o->event_label }}@if ($o->place) · {{ $o->place }}@endif</small><b>{{ $o->cardTitle() }}</b><em>{{ $o->who }}</em></span>
            </a>
          @endforeach
        </div>
      </section>
    @endif
    <a class="ld-all" href="{{ route('lab.list', $segment) }}">{{ __('site.lab.all_'.$segment) }} <span aria-hidden="true">←</span></a>
  </article>
  <dialog class="ld-lightbox" data-ld-lightbox aria-label="{{ __('site.lab.view_photo') }}"><img alt=""><button type="button" class="ld-x" aria-label="{{ __('site.awards.close') }}">×</button></dialog>
@else
  {{-- Publications: «الورقة البحثية» (css lab-paper, js sections/lab-paper) — a paper in a scientific journal: masthead,
       authors with initials (the Omani researcher highlighted), the key finding drawn after its kind (percent ring,
       before → after, range ruler or code cells), the abstract, the illustration as «شكل ١», margin notes and the citation. --}}
  @php
      $latin = strtr((string) $x->finding_value, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', '٪' => '%', '٫' => '.']);
      $finding = match (true) {
          (bool) preg_match('/^\s*(\d+(?:\.\d+)?)\s*%\s*$/u', $latin, $pm) => ['kind' => 'percent', 'value' => min(100, (float) $pm[1])],
          str_contains((string) $x->finding_value, '←') => ['kind' => 'shift', 'parts' => array_map('trim', explode('←', (string) $x->finding_value, 2))],
          (bool) preg_match('/^\s*\d+(?:\.\d+)?\s*[–-]\s*\d+(?:\.\d+)?\s*$/u', $latin) => ['kind' => 'range', 'parts' => array_map('trim', preg_split('/[–-]/u', (string) $x->finding_value, 2))],
          default => ['kind' => 'code'],
      };
      $authors = array_values(array_filter(array_map('trim', preg_split('/[،,]/u', (string) ($x->authors ?: $x->who)))));
      // The achiever among the authors: the author holding the whole «who», else the one whose name has its first word.
      $who = trim((string) $x->who);
      $whoFirst = (string) preg_split('/\s+/u', $who)[0];
      $featured = $who === '' ? false : collect($authors)->search(fn ($a) => str_contains($a, $who));
      if ($featured === false && $whoFirst !== '') {
          $featured = collect($authors)->search(fn ($a) => (bool) preg_match('/(^|\s)'.preg_quote($whoFirst, '/').'(\s|$)/u', $a));
      }
      // First letters of the first two words («ناصر العزري» → «نع»; «مسلم العريمي ضمن فريق بحثي» → «مع»).
      $initials = fn (string $n) => collect(preg_split('/\s+/u', trim($n)))->take(2)->map(fn ($w) => mb_substr(preg_replace('/^ال(?=\X)/u', '', $w), 0, 1))->implode('');
      $person = $x->achiever?->isLive() ? $x->achiever : null;
      $pcover = $x->image('cover');
  @endphp
  <article class="wrap lp" data-lab-item data-lab-paper>
    <div class="lp-mast">
      <span class="lp-mast-k">{{ __('site.lab.paper') }}@if ($x->field) · {{ $x->field }}@endif</span>
      @if ($x->journal)<b class="lp-mast-j">{{ $x->journal }}</b>@endif
      <span class="lp-mast-d">{{ $x->dateLabel() }}</span>
    </div>
    <div class="lp-sheet">
      <header class="lp-head">
        {{-- The platform's seal stamped on the paper: its ring turns slowly around the year. The phrase is sized to go once
             around the ring (no textLength: stretching the spacing breaks the joined Arabic letters). --}}
        <span class="lp-seal" aria-hidden="true"><svg viewBox="0 0 120 120"><defs><path id="lp-seal-p" d="M60 60m-43 0a43 43 0 1 1 86 0a43 43 0 1 1-86 0"/></defs>
          <circle class="lp-seal-o" cx="60" cy="60" r="57"/><circle class="lp-seal-i" cx="60" cy="60" r="34"/>
          <g class="lp-seal-r"><text><textPath href="#lp-seal-p">{{ __('site.lab.seal') }}</textPath></text></g></svg>
          <b>@if ($x->year()){{ $d($x->year()) }}@else✦@endif</b></span>
        <span class="lp-type">@if ($x->pub_type){{ $x->pub_type }}@else{{ __('site.lab.paper') }}@endif</span>
        <h1>{{ $x->title }}</h1>
        @if ($authors)
          <ul class="lp-authors" aria-label="{{ __('site.lab.authors') }}">
            @foreach ($authors as $i => $author)
              <li @class(['is-omani' => $i === $featured]) style="--i:{{ $i }}"><span class="lp-in" aria-hidden="true">{{ $initials($author) }}</span>{{ $author }}<sup>{{ $d($i + 1) }}</sup></li>
            @endforeach
          </ul>
          @if ($featured !== false && count($authors) > 1)<p class="lp-key"><i aria-hidden="true"></i>{{ __('site.lab.featured') }}</p>@endif
        @endif
      </header>

      <div class="lp-grid">
        <div class="lp-main">
          @if ($x->finding_value)
            <section class="lp-finding k-{{ $finding['kind'] }}" aria-labelledby="lp-finding-h" @if ($finding['kind'] === 'percent') style="--v: {{ $finding['value'] }}" @endif>
              <h2 id="lp-finding-h">{{ __('site.lab.finding') }}</h2>
              <div class="lp-viz">
                @switch($finding['kind'])
                  @case('percent')
                    <span class="lp-ring"><svg viewBox="0 0 120 120" aria-hidden="true"><circle class="lp-ring-bg" cx="60" cy="60" r="52" pathLength="100"/><circle class="lp-ring-fg" cx="60" cy="60" r="52" pathLength="100"/></svg><b>{{ $x->finding_value }}</b></span>
                    @break
                  @case('shift')
                    <span class="lp-shift"><s>{{ $finding['parts'][0] }}</s><i aria-hidden="true"><svg viewBox="0 0 60 20"><path d="M58 10H4M12 3 4 10l8 7"/></svg></i><b>{{ $finding['parts'][1] }}</b></span>
                    @break
                  @case('range')
                    <span class="lp-range"><span class="lp-axis" aria-hidden="true"><i></i></span><b>{{ $finding['parts'][0] }}</b><b>{{ $finding['parts'][1] }}</b></span>
                    @break
                  @default
                    <span class="lp-code" dir="ltr">@foreach (mb_str_split((string) $x->finding_value) as $i => $ch)<span style="--i:{{ $i }}">{{ $ch }}</span>@endforeach</span>
                @endswitch
              </div>
              @if ($x->finding_text)<p>{{ $x->finding_text }}</p>@endif
            </section>
          @endif

          <section class="lp-abstract" aria-labelledby="lp-abs-h">
            <h2 id="lp-abs-h">{{ __('site.lab.summary') }}</h2>
            <p>{{ $x->summary }}</p>
            @if ($x->body)<div class="lp-body">{!! str((string) $x->body)->sanitizeHtml() !!}</div>@endif
          </section>

          @if ($pcover)
            <figure class="lp-fig">
              <img src="{{ $x->imageUrl('cover') }}" alt="{{ \App\Models\LabItem::alt($pcover) }}" loading="lazy">
              <figcaption><b>{{ __('site.lab.figure') }}</b> {{ $x->title }}@if ($x->cover_is_illustrative) · <em>{{ __('site.lab.illustrative') }}</em>@endif</figcaption>
            </figure>
          @endif

          <section class="lp-cite" aria-labelledby="lp-cite-h">
            <h2 id="lp-cite-h">{{ __('site.lab.how_cite') }}</h2>
            <p class="lp-cite-t">{{ $x->citation() }}</p>
            <button type="button" class="li-cite lp-cite-b" data-cite="{{ $x->citation() }}" data-done="{{ __('site.lab.cite_copied') }}">{{ __('site.lab.copy_cite') }}</button>
          </section>

          @if ($x->sources->isNotEmpty())
            <section class="lp-refs" aria-labelledby="lp-refs-h"><h2 id="lp-refs-h">{{ __('site.lab.sources') }}</h2><ol>
              @foreach ($x->sources as $i => $s)<li><span>[{{ $d($i + 1) }}]</span>@if ($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener">{{ $s->title }}</a>@else{{ $s->title }}@endif</li>@endforeach
            </ol></section>
          @endif
        </div>

        <aside class="lp-margin" aria-label="{{ __('site.lab.notes') }}">
          <dl>
            @if ($x->journal)<div><dt>{{ __('site.lab.journal') }}</dt><dd>{{ $x->journal }}</dd></div>@endif
            <div><dt>{{ __('site.lab.published') }}</dt><dd>{{ $x->dateLabel() }}</dd></div>
            @if ($x->field)<div><dt>{{ __('site.lab.f_field') }}</dt><dd>{{ $x->field }}</dd></div>@endif
            @if (count($authors) > 1)<div><dt>{{ __('site.lab.authors') }}</dt><dd>{{ $d(count($authors)) }}</dd></div>@endif
          </dl>
          @if ($person)
            <a class="lp-person" href="{{ $person->url() }}"><span class="lp-in">@if ($av = $person->headerAvatarUrl())<img src="{{ $av }}" alt="">@else{{ $initials($person->name) }}@endif</span>
              <span><small>{{ __('site.lab.researcher') }}</small><b>{{ $person->name }}</b></span></a>
          @endif
        </aside>
      </div>
    </div>

    @if ($others->isNotEmpty())
      <section class="lp-more" aria-labelledby="lp-more-h">
        <h2 id="lp-more-h">{{ __('site.lab.other_'.$segment) }}</h2>
        <div class="lp-shelf">
          @foreach ($others as $o)
            <a class="lp-o" href="{{ $o->url() }}">
              <span class="lp-o-pic">@if ($img = $o->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif</span>
              <span class="lp-o-t"><small>{{ $o->field }} · {{ $o->dateLabel() }}</small>@if ($o->finding_value)<strong>{{ $o->finding_value }}</strong>@endif<b>{{ $o->title }}</b><em>{{ $o->who }}</em></span>
            </a>
          @endforeach
        </div>
      </section>
    @endif
    <a class="lp-all" href="{{ route('lab.list', $segment) }}">{{ __('site.lab.all_'.$segment) }} <span aria-hidden="true">←</span></a>
  </article>
@endif
</x-site.layout>
