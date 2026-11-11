{{-- Design index.html .home-inspiring: four inspiring achievers with their square cover. --}}
@php
    $people = \App\Models\Achiever::query()->published()->where('is_inspiring', true)->with(['media', 'field'])->orderBy('sort_order')->orderBy('id')->take(4)->get();
@endphp
@if ($people->isNotEmpty())
<section class="section wrap home-inspiring" id="inspiring" aria-labelledby="inspiring-title" tabindex="-1">
  <div class="inspiring-heading">
    <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="inspiring-title">{{ $section->text('title', __('site.homepage.inspiring')) }}</h2></div>
    @if ($lead = $section->text('lead', __('site.homepage.inspiring_lead')))<p>{{ $lead }}</p>@endif
  </div>
  <div class="inspiring-covers">
    @foreach ($people as $a)
      @php $cover = $a->image('inspiring_cover') ?? $a->image('portrait'); @endphp
      <figure class="inspiring-cover">
        <div class="inspiring-cover-art">@if ($cover)<img src="{{ $a->imageUrl($cover->collection_name) }}" alt="{{ __('site.homepage.inspiring_cover', ['name' => $a->name]) }}" loading="lazy" decoding="async" width="1254" height="1254">@endif</div>
        <figcaption><h3>{{ $a->name }}</h3><span>{{ $a->inspiring_field ?: $a->field?->name }}</span></figcaption>
      </figure>
    @endforeach
  </div>
</section>
@endif
