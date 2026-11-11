{{-- Design index.html .home-culture: people picked in the section's items (image = photo with focal point, label = badge). --}}
@php $items = $section->items()->with(['media', 'itemable'])->get(); @endphp
@if ($items->isNotEmpty())
<section class="section wrap home-culture" aria-labelledby="culture-title">
  <div class="sec-title"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt=""><h2 id="culture-title">{{ $section->text('title', __('site.homepage.culture')) }}</h2></div>
  @if ($sub = $section->text('lead'))<p class="culture-sub">{{ $sub }}</p>@endif
  <div class="row-more">
    <div class="people">@foreach ($items as $item)@php $img = $item->image('image'); $name = $item->displayTitle(); @endphp<a class="person sm" href="{{ $item->href() ?? route('achievers.index') }}" data-cat="{{ $item->itemable?->field?->name }}" data-name="{{ $name }}">@if ($img)<img src="{{ $item->imageUrl('image', 'card') }}" alt="{{ $name }}" loading="lazy" style="object-position:{{ $img->getCustomProperty('focal') ?: '50% 20%' }}">@endif @if ($item->label)<span class="badge">{{ $item->label }}</span>@endif<span class="name">{{ $name }}</span></a>@endforeach</div>
    <a class="more-round" href="{{ $section->setting('more_url') ?: route('achievers.index') }}" aria-label="{{ __('site.homepage.more_of', ['section' => $section->text('title', __('site.homepage.culture'))]) }}"><span class="btn-round"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg></span><span class="lbl">{{ __('site.homepage.more') }}</span></a>
  </div>
</section>
@endif
