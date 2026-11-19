@props(['letter', 'organization' => null, 'label' => null, 'emblem' => null, 'wideEmblem' => false])
{{-- Leader's word «نافذة المقام» (approved concept): the leader stands in an Omani arch with a gold frame and the
     organization's emblem as the keystone. The emblem swings on the frame; «اقرأ الكلمة كاملة» opens the word, the
     emblem becomes the reading marker and a signature closes it (css leader-window, js sections/leader-window).
     The quote is the letter's quote field (*stars* mark the gold highlight), else the first words of the word.
     Used for universities, institutions and the culture award (label / emblem given when there is no organization). --}}
@php
    $o = $organization;
    $photo = \App\Support\Media\Src::of($letter->image('photo'));
    $label ??= $o?->name;
    $emblem ??= $o?->logoUrl();
    $wideEmblem = $wideEmblem || (bool) $o?->hasWideLogo();
    $paragraphs = $letter->paragraphs();
    $quote = trim((string) $letter->quote);
    if ($quote === '' && $paragraphs !== []) {
        $quote = \Illuminate\Support\Str::words($paragraphs[0], 16, '…');
    }
    $plain = preg_replace('/^[\s…]+|[\s…]+$/u', '', str_replace('*', '', $quote));
    $quoteHtml = preg_replace('/\*(.+?)\*/u', '<mark>$1</mark>', e($quote));
    $words = count(preg_split('/\s+/u', trim(implode(' ', $paragraphs)), -1, PREG_SPLIT_NO_EMPTY));
    $minutes = max(1, (int) round($words / 150));
    $nameWords = preg_split('/\s+/u', trim((string) $letter->name), -1, PREG_SPLIT_NO_EMPTY);
    $arch = 'M438 598 V230 C438 124 350 42 220 6 C90 42 2 124 2 230 V598';
    $arrow = fn (string $d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'.$d.'"/></svg>';
@endphp
<section class="ww" aria-labelledby="ww-name" data-word-window>
  <svg class="ww-defs" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <clipPath id="ww-arch" clipPathUnits="objectBoundingBox"><path d="M.9636 1 V.3933 C.9636 .2333 .7818 .1033 .5 .0433 C.2182 .1033 .0364 .2333 .0364 .3933 V1 Z"/></clipPath>
      <linearGradient id="ww-gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFF1D2"/><stop offset=".45" stop-color="#E6C177"/><stop offset=".75" stop-color="#C6A15A"/><stop offset="1" stop-color="#F4DFAE"/></linearGradient>
    </defs>
  </svg>
  <canvas class="ww-dust" aria-hidden="true"></canvas>
  <div class="ww-in">
    <figure class="ww-window" role="img" aria-label="{{ trim($letter->title.' '.$letter->name) }}">
      <div class="ww-tilt">
        <div class="ww-glass">
          <div class="ww-glow"></div>
          <div class="ww-star"></div>
          @if ($photo)<div class="ww-photo"><img src="{{ $photo }}" alt="" loading="lazy"></div>@endif
          <div class="ww-sweep"></div>
        </div>
        <svg class="ww-frame" viewBox="0 0 440 600" aria-hidden="true" focusable="false">
          <path class="ww-line-out" pathLength="1" d="{{ $arch }}"/>
          <path class="ww-line-in" pathLength="1" d="M424 598 V236 C424 140 344 62 220 26 C96 62 16 140 16 236 V598"/>
          @foreach (range(1, 3) as $i)<path class="ww-trail" pathLength="1" d="{{ $arch }}"/>@endforeach
          <path class="ww-progress" pathLength="1" d="{{ $arch }}"/>
        </svg>
        @if ($emblem)<span class="ww-key"><span @class(['ww-key-in', 'is-wide' => $wideEmblem])><img src="{{ $emblem }}" alt=""></span></span>@endif
        <span class="ww-sill"></span>
      </div>
    </figure>

    <div class="ww-copy">
      @if ($label)<span class="ww-eyebrow"><i></i>{{ $label }}</span>@endif
      @if ($letter->title)<small class="ww-title">{{ $letter->title }}</small>@endif
      <h3 class="ww-name" id="ww-name">@foreach ($nameWords as $i => $word)<span class="ww-w"><span style="--i:{{ $i }}">{{ $word }}</span></span> @endforeach</h3>
      @if ($letter->role)<p class="ww-role">{{ $letter->role }}</p>@endif
      @if ($quote !== '')<blockquote class="ww-quote" data-ww-words>{!! $quoteHtml !!}</blockquote>@endif
      <div class="ww-meta">
        <button type="button" class="ww-cta" aria-expanded="false" aria-controls="ww-full">{{ __('site.university.word_read') }} {!! $arrow('M12 5v14M6 13l6 6 6-6') !!}</button>
        <small>{{ __('site.university.word_time', ['time' => \App\Support\ArabicCount::of($minutes, 'minutes')]) }}</small>
      </div>

      <div class="ww-full" id="ww-full" aria-hidden="true" inert>
        <div>
          <div class="ww-text">
            @foreach ($paragraphs as $paragraph)
              @php
                  $html = e($paragraph);
                  if ($plain !== '' && str_contains($paragraph, $plain)) {
                      $html = str_replace(e($plain), '<span class="ww-hl">'.e($plain).'</span>', $html);
                  }
              @endphp
              <p class="ww-p">{!! $html !!}</p>
            @endforeach
            @if ($letter->source_text)<p class="ww-src">{{ __('site.university.source') }} @if ($letter->source_url)<a href="{{ $letter->source_url }}" target="_blank" rel="noopener">{{ $letter->source_text }}</a>@else{{ $letter->source_text }}@endif</p>@endif
            <div class="ww-sign">
              <small>{{ __('site.university.word_regards') }}</small>
              <b>{{ $letter->name }}</b>
              <svg viewBox="0 0 300 22" aria-hidden="true" focusable="false"><path pathLength="1" d="M296 12 C230 2 170 20 110 12 S30 4 4 14"/></svg>
              @if ($letter->role)<em>{{ $letter->role }}</em>@endif
            </div>
            <button type="button" class="ww-close">{{ __('site.university.word_hide') }} {!! $arrow('M12 19V5M6 11l6-6 6 6') !!}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
