{{-- Design: universities.html. The hero map (js universities-hero) reads window.INJAZ_UNIS below. --}}
@php
    $unis = \App\Models\Organization::query()->listed()->ofType('academic')->with(['units' => fn ($q) => $q->where('kind', 'branch')])->get()
        ->map(fn ($o) => ['slug' => $o->slug, 'group' => $o->academic_group, 'city' => (string) $o->city, 'branches' => $o->units->pluck('name')->values()->all()])->values()->all();
    $title = preg_replace('/\*(.+?)\*/u', '<span>$1</span>', e($page->hero_title ?: $page->title));
@endphp
<x-site.layout :page="$page" styles="universities" body-class="flush" class="u-page">
  <section class="uh" aria-labelledby="uh-title" data-uh wire:ignore>
    <div class="uh-bg" aria-hidden="true"><i></i><i></i><i></i></div>
    <div class="uh-main">
      <div class="uh-text">
        @if ($page->hero_eyebrow)<span class="uh-eyebrow"><i aria-hidden="true"></i>{{ $page->hero_eyebrow }}</span>@endif
        <h1 id="uh-title">{!! $title !!}</h1>
        @if ($page->hero_lead)<p>{{ $page->hero_lead }}</p>@endif
        <div class="uh-stats" role="group" aria-label="{{ __('site.organizations.stats_label') }}" data-uh-stats></div>
      </div>
      <div class="uh-map" data-uh-map aria-label="{{ __('site.organizations.map_label') }}">
        <p class="uh-tag" aria-live="polite" data-uh-tag hidden></p>
      </div>
    </div>
  </section>
  <script>window.INJAZ_UNIS = {!! json_encode($unis, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};</script>
  <livewire:site.universities-index :site-page="$page" />
</x-site.layout>
