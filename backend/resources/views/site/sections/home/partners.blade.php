{{-- Design index.html .home-partners: organizations marked «شريك» (drafts too: logo without a link) in rows by type, each row a marquee (two copies of the
     logos; short rows repeat their logos so the band is full, as in the design). --}}
@php
    $rows = \App\Models\Organization::query()->where('is_partner', true)->with(['media', 'type'])
        ->orderBy('sort_order')->orderBy('id')->get()->groupBy('organization_type_id')
        ->sortBy(function ($orgs) use ($section) {
            // Rows in the section's order (row_order: list of type slugs; jsonb does not keep key order), then by type.
            $pos = array_search($orgs->first()->type?->slug, (array) $section->setting('row_order', []), true);

            return ($pos === false ? 100 : $pos) * 1000 + ($orgs->first()->type?->sort_order ?? 0);
        });
    $modifier = ['gov' => 'government', 'private' => 'private', 'military' => 'military', 'sports' => 'sports'];
@endphp
@if ($rows->isNotEmpty())
<section class="section wrap home-partners" aria-labelledby="partners-title">
  <div class="partners-head">
    <span class="partners-eyebrow">{{ $section->text('eyebrow', __('site.homepage.partners_eyebrow')) }}</span>
    <h2 id="partners-title">{{ $section->text('title', __('site.homepage.partners')) }}</h2>
    <span class="partners-line" aria-hidden="true"></span>
  </div>
  @foreach ($rows as $orgs)
    @php
        $type = $orgs->first()->type;
        $title = $section->text('rows.'.$type?->slug) ?: ($type?->group_label ?: $type?->name);
        $band = $orgs->count() >= 10 ? $orgs : $orgs->concat($orgs)->concat($orgs);
    @endphp
    <div class="partners-row partners-row--{{ $modifier[$type?->slug] ?? $type?->slug }}"><h3>{{ $title }}</h3><div class="partners-marquee" tabindex="0" aria-label="{{ __('site.homepage.logos_of', ['name' => $title]) }}"><div class="partners-track">
      @foreach ([false, true] as $copy)
        <div class="partners-group" @if ($copy) aria-hidden="true" @endif>@foreach ($band as $k => $o)@php $logo = $o->logoUrl(); $alt = $copy ? '' : $o->name; @endphp
          @if ($o->isLive())<a class="partner" href="{{ $o->url() }}" title="{{ $o->name }}" @if ($copy) tabindex="-1" @endif>@else<div class="partner" title="{{ $o->name }}">@endif
          @if ($logo)<img src="{{ $logo }}" alt="{{ $alt }}" loading="{{ $loop->parent->first && $k < 6 ? 'eager' : 'lazy' }}" decoding="async">@endif
          @if ($o->isLive())</a>@else</div>@endif
        @endforeach</div>
      @endforeach
    </div></div></div>
  @endforeach
  <a class="partners-all" href="{{ route('organizations.index') }}">{{ $section->text('more_label', __('site.homepage.all_organizations')) }} <span aria-hidden="true">←</span></a>
</section>
@endif
