{{-- «ملف المؤسسة» (ministries, military, companies; css org-profile, js sections/org-profile). The front is like the federations':
     the logo centered (over the cover photo «صورة الغلاف» when there is one), the type and founding year, the name, the English
     name, the areas and the in-page links. Then: about (framed, whole) and «بطاقة المؤسسة», its achievements (linked to it or to
     its people) with its news — up to eight —, the leader's word, «المؤسسة في سطور» (numbers, path of names and decrees,
     successive heads), its people, units, gallery and videos, sources and the other institutions (framed). Every part appears
     only when it has content; the achievements always do (with an invitation when none is documented yet). --}}
@php
    $o = $organization;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $plural = fn ($n, $noun) => \App\Support\ArabicCount::of($n, $noun);
    $key = $o->type?->cssKey() ?? 'gov';
    $about = array_values(array_filter(preg_split('/\R\s*\R/u', trim((string) $o->about))));
    $letter = $o->letter?->is_published ? $o->letter : null;
    $cover = $o->image('cover');
    $en = (string) $o->getTranslation('name', 'en', false);
    $en = $en !== '' && $en !== $o->name ? $en : null;
    $areas = $o->focusAreaLabels();
    $leader = $o->leaders->firstWhere('is_current', true);
    // organizations.details: the card's label for the year (e.g. «المسمى الحالي»), vision/mission cards, priorities, heads note.
    $details = $o->details ?? [];
    $foundedLabel = filled($details['founded_label'] ?? null) ? $details['founded_label'] : null;
    $direction = array_values(array_filter($details['direction'] ?? [], fn ($c) => filled($c['title'] ?? null)));
    $priorities = array_values(array_filter($details['priorities'] ?? [], 'filled'));
    $place = $o->hq ?: $o->city;
    $host = $o->website ? preg_replace('/^www\./', '', (string) parse_url($o->website, PHP_URL_HOST)) : null;
    $achievementCount = $feed->where('kind', 'achievement')->count();
    $photos = $o->getMedia('gallery');
    $hasGallery = $photos->isNotEmpty() || $o->videos->isNotEmpty();
    // «بطاقة المؤسسة»: shown beside the about text, or alone when the organization has details but no text.
    $card = array_values(array_filter([
        ['label' => __('site.organizations.c_name'), 'value' => $o->name],
        $en ? ['label' => __('site.organizations.c_name_en'), 'value' => $en, 'ltr' => true] : null,
        $o->type ? ['label' => __('site.organizations.f_sector'), 'value' => $o->type->label] : null,
        $o->founded ? ['label' => $foundedLabel ?? __('site.organizations.f_founded'), 'value' => $d($o->founded)] : null,
        $place ? ['label' => __('site.organizations.f_place'), 'value' => $place] : null,
        $leader ? ['label' => __('site.organizations.c_leader'), 'value' => collect([$leader->title, $leader->name])->filter()->implode(' '), 'note' => $leader->role] : null,
        $areas ? ['label' => __('site.organizations.c_areas'), 'value' => implode('، ', $areas)] : null,
        $host ? ['label' => __('site.organizations.f_website'), 'value' => $host, 'url' => $o->website, 'ltr' => true] : null,
    ]));
    $hasAbout = $about || count($card) > 2;
    // «المؤسسة في سطور» (under the leader's word): its numbers, its milestones (names and decrees) and its successive heads.
    $heads = $o->leaders->where('kind', 'president')->sortBy(fn ($l) => [$l->from_year ?? 9999, $l->sort_order])->values();
    $hasStory = $o->stats->isNotEmpty() || $direction || $priorities || $o->milestones->isNotEmpty() || $heads->isNotEmpty();
    $jump = array_filter([
        'op-about' => $hasAbout ? __('site.organizations.about_title') : null,
        'op-ach' => __('site.organizations.achievements'),
        'op-word' => $letter ? __('site.organizations.word') : null,
        'op-story' => $hasStory ? __('site.organizations.story') : null,
        'op-people' => $people->isNotEmpty() ? __('site.organizations.people') : null,
        'op-units' => $o->units->isNotEmpty() ? __('site.organizations.units') : null,
        'op-gal' => $hasGallery ? __('site.organizations.gallery') : null,
        'op-others' => $sameField->isNotEmpty() ? __('site.organizations.others') : null,
    ]);
    $shown = $feed->take(8);
@endphp
<x-site.layout :title="$o->name" :description="$o->tagline ?: \Illuminate\Support\Str::limit(strip_tags((string) $o->about), 160)"
    :image="$o->imageUrl('cover')" styles="institution" body-class="flush op-page" :noindex="! $o->isLive()"
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index')]">
<article class="op s-{{ $key }}" data-org-profile @if ($o->color_primary) style="--brand: {{ $o->color_primary }}" @endif>
  <header @class(['op-head', 'has-cover' => $cover])>
    @if ($cover)<img class="op-cover" src="{{ $o->imageUrl('cover') }}" alt="" fetchpriority="high" style="object-position: {{ \App\Models\Organization::focal($cover) }}">@endif
    <span class="op-logo"><x-site.organization.emblem :organization="$o" :alt="__('site.organizations.logo_of', ['name' => $o->name])" :lazy="false" /></span>
    @if ($o->type)<span class="op-type">{{ $o->type->label }}@if ($o->founded && ! $foundedLabel) – {{ __('site.organizations.founded_in', ['year' => $d($o->founded)]) }}@endif</span>@endif
    <h1>{{ $o->name }}</h1>
    @if ($en)<p class="op-en" dir="ltr" lang="en">{{ $en }}@if ($o->abbr) ({{ $o->abbr }})@endif</p>@endif
    @if ($o->tagline)<p class="op-line">{{ $o->tagline }}</p>@endif
    @if ($areas || $o->website)
      <ul class="op-areas" aria-label="{{ __('site.organizations.c_areas') }}">@foreach ($areas as $area)<li>{{ $area }}</li>@endforeach
        @if ($o->website)<li><a href="{{ $o->website }}" target="_blank" rel="noopener">{{ __('site.organizations.f_website') }} ↗</a></li>@endif</ul>
    @endif
    @if (count($jump) > 1)
      <nav class="op-jump" aria-label="{{ __('site.university.jump') }}" data-op-jump><div>@foreach ($jump as $id => $label)<a href="#{{ $id }}">{{ $label }}</a>@endforeach</div></nav>
    @endif
  </header>

  @if ($hasAbout)
    <section class="wrap op-sec op-about" id="op-about" aria-labelledby="op-about-h">
      <div @class(['op-about-grid', 'no-text' => ! $about])>
        <div class="op-about-main">
          <div class="op-h"><span class="op-kick">{{ $o->displayName() }}</span><h2 id="op-about-h">{{ __('site.organizations.about_title') }}</h2></div>
          @if ($about)<div class="op-text">@foreach ($about as $paragraph)<p>{{ $paragraph }}</p>@endforeach</div>@endif
        </div>
        <aside class="op-card" aria-label="{{ __('site.organizations.card') }}">
          <h3><x-site.organization.glyph :kind="$key" />{{ __('site.organizations.card') }}</h3>
          <dl>
            @foreach ($card as $row)
              <div><dt>{{ $row['label'] }}</dt><dd @if (! empty($row['ltr'])) dir="ltr" @endif>@if (isset($row['url']))<a href="{{ $row['url'] }}" target="_blank" rel="noopener">{{ $row['value'] }} <span aria-hidden="true">↗</span></a>@else{{ $row['value'] }}@endif
                @if (! empty($row['note']))<small>{{ $row['note'] }}</small>@endif</dd></div>
            @endforeach
          </dl>
        </aside>
      </div>
    </section>
  @endif

  <section class="wrap op-sec op-ach" id="op-ach" aria-labelledby="op-ach-h">
    <div class="op-h"><span class="op-kick">{{ __('site.organizations.record') }}</span><h2 id="op-ach-h">{{ __('site.organizations.achievements') }}</h2>
      @if ($achievementCount)<small>{{ $plural($achievementCount, 'achievements') }}</small>@endif</div>
    @if ($shown->isNotEmpty())
      <div class="op-grid">
        @foreach ($shown as $item)
          <a class="op-card-a" href="{{ $item['url'] }}">
            <span class="op-img">@if ($item['image'])<img src="{{ $item['image'] }}" alt="" loading="lazy" style="object-position: {{ $item['focal'] }}">@endif</span>
            <span class="op-pill k-{{ $item['kind'] }}">{{ $item['tag'] ?? __('site.organizations.news_tag') }}</span><b>{{ $item['title'] }}</b>
            <small>{{ collect([$item['who'], $item['label']])->filter()->implode(' · ') }}</small>
          </a>
        @endforeach
      </div>
      @if ($feed->count() > 8)
        <a class="op-more" href="{{ route('organizations.achievements', $o) }}">{{ __('site.organizations.all_count', ['n' => $d($feed->count())]) }} <span aria-hidden="true">←</span></a>
      @endif
    @else
      <div class="op-empty">
        <span class="op-empty-i" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M24 31c-5.5 0-10-4.5-10-10V8h20v13c0 5.5-4.5 10-10 10Z"/><path d="M14 12H8v4a7 7 0 0 0 7 7M34 12h6v4a7 7 0 0 1-7 7M24 31v7M16 42h16M19 38h10"/></svg></span>
        <p><b>{{ __('site.organizations.empty_title') }}</b><span>{{ __('site.organizations.empty_text') }}</span></p>
        <a href="{{ url('/contact') }}">{{ __('site.organizations.empty_cta') }} <span aria-hidden="true">←</span></a>
      </div>
    @endif
  </section>

  @if ($letter)
    <section class="wrap op-sec o-word" id="op-word" aria-labelledby="o-word-h">
      <div class="op-h"><span class="op-kick">{{ $o->displayName() }}</span><h2 id="o-word-h">{{ __('site.organizations.word') }}</h2></div>
      <x-site.organization.word-window :letter="$letter" :organization="$o" />
    </section>
  @endif

  @if ($hasStory)
    <section class="wrap op-sec op-story" id="op-story" aria-labelledby="op-story-h" data-op-story>
      <div class="op-h"><span class="op-kick">{{ $o->displayName() }}</span><h2 id="op-story-h">{{ __('site.organizations.story') }}</h2></div>
      <div class="op-story-box">
        @if ($o->stats->isNotEmpty())
          <ul class="op-ribbon" aria-label="{{ __('site.organizations.numbers') }}">
            @foreach ($o->stats as $stat)<li style="--i: {{ $loop->index }}"><b>{{ $stat->value }}</b><span>{{ $stat->label }}</span></li>@endforeach
          </ul>
        @endif
        @if ($direction)
          <div class="op-direction">@foreach ($direction as $item)<article>@if (filled($item['label'] ?? null))<span>{{ $item['label'] }}</span>@endif<h3>{{ $item['title'] }}</h3>@if (filled($item['text'] ?? null))<p>{{ $item['text'] }}</p>@endif</article>@endforeach</div>
        @endif
        @if ($priorities)
          <div class="op-goals"><h3>{{ __('site.organizations.priorities') }}</h3><ul>@foreach ($priorities as $priority)<li>{{ $priority }}</li>@endforeach</ul></div>
        @endif
        @if ($o->milestones->isNotEmpty())
          <div class="op-path">
            <h3>{{ __('site.organizations.path') }}</h3>
            <ol>
              @foreach ($o->milestones as $milestone)
                @php preg_match('/(?:المرسوم السلطاني|مرسوم)[^٠-٩0-9]*([٠-٩0-9]+\s*\/\s*[٠-٩0-9]+)/u', (string) $milestone->text, $decree); @endphp
                <li @class(['is-now' => $loop->last]) style="--i: {{ $loop->index }}">
                  <span class="op-y">@if ($milestone->year){{ $d($milestone->year) }}@endif</span>
                  <div class="op-step"><b>{{ $milestone->title }}</b>
                    @if ($milestone->decree)<span class="op-decree">{{ $milestone->decree }}</span>@elseif ($decree)<span class="op-decree">{{ __('site.organizations.decree', ['n' => $decree[1]]) }}</span>@endif
                    @if ($milestone->text)<p>{{ $milestone->text }}</p>@endif @if ($milestone->place)<small>{{ $milestone->place }}</small>@endif</div>
                </li>
              @endforeach
            </ol>
          </div>
        @endif
        @if ($heads->isNotEmpty())
          <x-site.organization.heads-stage :heads="$heads" :title="__('site.organizations.heads')" :note="$details['heads_note'] ?? null" />
        @endif
      </div>
    </section>
  @endif

  @if ($people->isNotEmpty())
    <section class="wrap op-sec op-people" id="op-people" aria-labelledby="op-people-h">
      <div class="op-h"><span class="op-kick">{{ __('site.organizations.people_kick') }}</span><h2 id="op-people-h">{{ __('site.organizations.people') }}</h2><small>{{ $plural($people->count(), 'achievers') }}</small></div>
      <div class="op-people-row">
        @foreach ($people as $person)
          <a class="op-person" href="{{ $person->url() }}">
            <span class="op-ph">@if ($img = $person->imageUrl('portrait', 'card'))<img src="{{ $img }}" alt="" loading="lazy" style="object-position: {{ \App\Models\Achiever::focal($person->image('portrait')) }}">@endif</span>
            <b>{{ $person->name }}</b>
            <small>{{ collect([\App\Models\Organization::affiliationLabel($person->pivot->kind, $person->gender), $person->field?->name])->filter()->implode(' · ') }}</small>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  @if ($o->units->isNotEmpty())
    <section class="wrap op-sec op-units" id="op-units" aria-labelledby="op-units-h">
      <div class="op-h"><h2 id="op-units-h">{{ __('site.organizations.units') }}</h2></div>
      @foreach ($o->units->groupBy(fn ($unit) => (string) $unit->group_label) as $label => $units)
        <div class="op-unit-g">@if ($label !== '')<h3>{{ $label }}</h3>@endif
          <ul>@foreach ($units as $unit)<li><b>{{ $unit->name }}</b>@if ($unit->year)<small>{{ $d($unit->year) }}</small>@endif @if ($unit->note)<span>{{ $unit->note }}</span>@endif</li>@endforeach</ul></div>
      @endforeach
    </section>
  @endif


  @if ($hasGallery)
    <section class="wrap op-sec op-gal" id="op-gal" aria-labelledby="op-gal-h">
      <div class="op-h"><h2 id="op-gal-h">{{ __('site.organizations.gallery') }}</h2></div>
      @if ($photos->isNotEmpty())
        <div class="op-photos">
          @foreach ($photos->take(9) as $photo)
            @php $caption = $photo->getCustomProperty('title') ?? \App\Support\Localized::get($photo->getCustomProperty('caption')); @endphp
            <figure><button type="button" class="op-zoom" data-op-zoom aria-label="{{ __('site.lab.view_photo') }}"><img src="{{ \App\Support\Media\Src::of($photo) }}" alt="{{ $caption }}" loading="lazy"></button>
              @if ($caption)<figcaption>{{ $caption }}@if ($year = $photo->getCustomProperty('year')) · {{ $d($year) }}@endif</figcaption>@endif</figure>
          @endforeach
        </div>
      @endif
      @if ($o->videos->isNotEmpty())
        <div class="op-videos">
          @foreach ($o->videos as $video)
            <a class="op-video" href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" target="_blank" rel="noopener">
              <span class="op-img"><img src="{{ $video->thumbnail() }}" alt="" loading="lazy"><i aria-hidden="true"></i></span>
              <b>{{ $video->title }}</b>@if ($video->year)<small>{{ $d($video->year) }}</small>@endif
            </a>
          @endforeach
        </div>
      @endif
      <dialog class="op-lightbox" data-op-lightbox aria-label="{{ __('site.lab.view_photo') }}"><img alt=""><button type="button" class="op-x" aria-label="{{ __('site.organizations.close') }}">×</button></dialog>
    </section>
  @endif

  @if ($o->sources->isNotEmpty())
    <p class="wrap op-sources">{{ __('site.university.sources') }} @foreach ($o->sources as $source)@if ($source->url)<a href="{{ $source->url }}" target="_blank" rel="noopener">{{ $source->title }}</a>@else{{ $source->title }}@endif{{ $loop->last ? '' : ' · ' }}@endforeach</p>
  @endif

  @if ($sameField->isNotEmpty())
    <section class="wrap op-sec op-others" id="op-others" aria-labelledby="op-others-h">
      <div class="op-h"><h2 id="op-others-h">{{ __('site.organizations.others') }}</h2></div>
      <div class="op-frame" data-org-rail><div class="rail-view"><div class="rail-track op-row">@foreach ($sameField as $other)<x-site.cards.organization :organization="$other" />@endforeach</div></div></div>
      <a class="op-more" href="{{ route('organizations.index') }}">{{ __('site.organizations.more_orgs') }} <span aria-hidden="true">←</span></a>
    </section>
  @endif
</article>
</x-site.layout>
