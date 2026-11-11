{{-- Design: institutions.html. Hero slides and His Majesty's letter come from the page (dashboard: صفحات الموقع). --}}
@php $slides = $page->getMedia('hero'); $letter = $page->letter?->is_published ? $page->letter : null; @endphp
<x-site.layout :page="$page" styles="organizations" body-class="orgs-page">
  <section class="orgs-hero frame-hero" data-sultan-hero>
    <div class="fh-frame">
      @foreach ($slides as $slide)
        @php $url = \App\Support\Media\Src::of($slide); @endphp
        <figure @class(['fh-slide', 'on' => $loop->first]) style="--bg:url({{ $url }})"><img src="{{ $url }}" alt="{{ $slide->getCustomProperty('alt') }}" @unless ($loop->first) loading="lazy" @endunless></figure>
      @endforeach
    </div>
    @if ($slides->count() > 1)
      <div class="fh-bars" aria-label="{{ __('site.organizations.slider') }}">
        @foreach ($slides as $slide)
          <button type="button" @class(['fh-bar', 'on' => $loop->first]) aria-label="{{ __('site.organizations.slide_n', ['n' => $loop->iteration]) }}" @if ($loop->first) aria-current="true" @endif><i></i></button>
        @endforeach
      </div>
    @endif
    <div class="fh-text">
      <h1>{{ $page->hero_title ?: $page->title }}</h1>
      @if ($page->hero_lead)<p>{{ $page->hero_lead }}</p>@endif
    </div>
    @if ($letter)
      @php $paper = \App\Support\Media\Src::of($letter->image('photo')); @endphp
      <button type="button" class="ol-card" data-letter-open aria-haspopup="dialog" aria-label="{{ __('site.organizations.letter_open') }}">
        @if ($paper)<span class="ol-paper"><img src="{{ $paper }}" alt="" width="900" height="1200" decoding="async"></span>@endif
        <span class="ol-cap"><b>{{ __('site.organizations.letter_kicker') }}</b>@if ($letter->quote)<span>{{ $letter->quote }}</span>@endif<em>{{ __('site.organizations.letter_read') }}</em></span>
      </button>
    @endif
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
