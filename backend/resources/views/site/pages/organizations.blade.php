{{-- «ديوان المؤسسات» (css orgs-diwan, js sections/orgs-diwan). The hero: a short band of His Majesty's visits (a slow
     slideshow, no text on the photos) under a white card with the title, His Majesty's handwritten letter and the number
     of organizations per type; then the list (livewire site.organizations-index). Slides and letter come from the page
     (dashboard: صفحات الموقع). --}}
@php
    $slides = $page->getMedia('hero');
    $letter = $page->letter?->is_published ? $page->letter : null;
    $paper = $letter ? \App\Support\Media\Src::of($letter->image('photo')) : null;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
@endphp
<x-site.layout :page="$page" styles="organizations" body-class="orgs-page">
  <section @class(['od-hero', 'no-photos' => $slides->isEmpty()]) data-od-hero aria-labelledby="od-title">
    @if ($slides->isNotEmpty())
      <div class="od-band">
        @foreach ($slides as $slide)
          <figure @class(['od-slide', 'on' => $loop->first]) style="--k: {{ $loop->index % 3 }}">
            <img src="{{ \App\Support\Media\Src::of($slide) }}" alt="{{ $slide->getCustomProperty('alt') }}" style="object-position: {{ $slide->getCustomProperty('focal') ?: '50% 30%' }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
          </figure>
        @endforeach
        @if ($slides->count() > 1)
          <div class="od-bars" role="group" aria-label="{{ __('site.organizations.slider') }}">
            @foreach ($slides as $slide)
              <button type="button" @class(['od-bar', 'on' => $loop->first]) aria-label="{{ __('site.organizations.slide_n', ['n' => $d($loop->iteration)]) }}" @if ($loop->first) aria-current="true" @endif><i></i></button>
            @endforeach
          </div>
        @endif
      </div>
    @endif
    <div class="od-head">
      <div class="od-head-t">
        <span class="od-eyebrow">{{ __('site.organizations.eyebrow') }}</span>
        <h1 id="od-title">{{ $page->hero_title ?: $page->title }}</h1>
        @if ($page->hero_lead)<p class="od-lead">{{ $page->hero_lead }}</p>@endif
      </div>
      @if ($types->isNotEmpty())
        <ul class="od-ledger" data-orgs-pulse aria-label="{{ __('site.organizations.ledger') }}">
          @foreach ($types as $type)
            <li><a href="#od-{{ $type->slug }}"><b data-count="{{ $type->organizations_count }}">{{ $d($type->organizations_count) }}</b><span>{{ $type->chipLabel() }}</span></a></li>
          @endforeach
        </ul>
      @endif
      @if ($letter)
        <button type="button" class="od-letter" data-letter-open aria-haspopup="dialog" aria-label="{{ __('site.organizations.letter_open') }}">
          @if ($paper)<span class="od-paper"><img src="{{ $paper }}" alt="" width="900" height="1200" decoding="async"></span>@endif
          <span class="od-letter-t"><small>{{ __('site.organizations.letter_kicker') }}</small>@if ($letter->quote)<q>{{ $letter->quote }}</q>@endif<em>{{ __('site.organizations.letter_read') }} <span aria-hidden="true">←</span></em></span>
        </button>
      @endif
    </div>
  </section>
  @if ($letter)
    <dialog class="ol-dialog" data-letter aria-labelledby="ol-h">
      <button type="button" class="ol-x" data-letter-close aria-label="{{ __('site.organizations.close') }}">×</button>
      <div class="ol-in">
        @if ($paper)<img src="{{ $paper }}" alt="{{ \App\Models\Letter::alt($letter->image('photo'), (string) $letter->title) }}" width="900" height="1200">@endif
        <div class="ol-txt">
          @if ($letter->title)<h2 id="ol-h">{{ $letter->title }}</h2>@endif
          @if ($letter->meta)<p class="ol-meta">{{ $letter->meta }}</p>@endif
          @foreach ($letter->paragraphs() as $paragraph)<p>{{ $paragraph }}</p>@endforeach
          @if ($letter->name)<p class="ol-sign">{{ $letter->name }}@if ($letter->role)<br><span>{{ $letter->role }}</span>@endif</p>@endif
        </div>
      </div>
    </dialog>
  @endif

  <livewire:site.organizations-index :site-page="$page" />
</x-site.layout>
