@php
    $o = $organization;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $icons = [
        '' => '<path d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z"/>',
        'student' => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c3 2.5 9 2.5 12 0v-5"/>',
        'staff' => '<path d="M4 19V6a2 2 0 0 1 2-2h12v15H6a2 2 0 0 0-2 2"/><path d="M8 8h6M8 11h4"/>',
        'org' => '<path d="M3 20h18M5 20V10l7-5 7 5v10"/><path d="M9 20v-5h6v5"/>',
    ];
@endphp
<div>
  <section class="uah">
    @if ($cover = $o->imageUrl('cover', 'large'))<img class="uah-bg" src="{{ $cover }}" alt="">@endif
    <span class="uah-in"><a class="uah-logo" href="{{ $o->url() }}">@if ($logo = $o->logoUrl())<img src="{{ $logo }}" alt="{{ $o->name }}">@endif</a>
      <span class="uah-k">{{ __('site.university.news_and_achievements') }}</span><h1>{{ __('site.university.achievements_of', ['name' => $o->name]) }}</h1>
      <span class="uah-stats">@foreach ($totals as $total => $n)<span><b>{{ $d($n) }}</b>{{ __('site.university.types_'.$total) }}</span>@endforeach</span></span>
  </section>
  <div class="wrap">
    <div class="ua-filters">
      <div class="uat" role="group" aria-label="{{ __('site.university.filter_owner') }}">
        @foreach (\App\Livewire\Site\OrganizationAchievements::TYPES as $t)
          <button type="button" wire:click="setType('{{ $t }}')" aria-pressed="{{ $type === $t ? 'true' : 'false' }}"><span class="uat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$t] !!}</svg></span><b>{{ $t ? __('site.university.type_'.$t) : __('site.university.all') }}</b><small>{{ $d($counts[$t]) }}</small></button>
        @endforeach
      </div>
      <div class="uag" role="group" aria-label="{{ __('site.university.filter_gender') }}"><span>{{ __('site.university.filter') }}</span>
        @foreach (['m', 'f'] as $g)<button type="button" wire:click="toggleGender('{{ $g }}')" aria-pressed="{{ $gender === $g ? 'true' : 'false' }}">{{ __('site.university.gender_'.$g) }}</button>@endforeach
      </div>
    </div>
    <p class="ua-count" aria-live="polite">{{ $items->isEmpty() ? __('site.university.no_achievements_count') : \App\Support\ArabicCount::of($items->count(), 'achievements') }}</p>
    <div class="uacs">
      @forelse ($items->take($limit) as $row)
        @php $a = $row['achievement']; $who = $a->primaryAchiever()?->name ?? $o->name; @endphp
        <a class="uac" href="{{ $a->url() }}" wire:key="ua-{{ $a->id }}"><span class="uac-img">@if ($img = $a->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy" style="object-position:{{ \App\Models\Achievement::focal($a->image('cover')) }}">@endif
          <span class="un-k un-{{ $row['type'] }}">{{ __('site.university.type_'.$row['type']) }}</span></span>
          <span class="uac-b"><b>{{ $a->title }}</b><small>{{ collect([$who, $a->dateLabel()])->filter()->implode(' · ') }}</small>@if ($a->excerpt)<em>{{ $a->excerpt }}</em>@endif</span></a>
      @empty
        <p class="u-soon">{{ __('site.university.no_match'.($type === 'student' ? '_student' : '')) }}</p>
      @endforelse
    </div>
    @if ($items->count() > $limit)<button type="button" class="ua-more" wire:click="more">{{ __('site.university.more', ['n' => $d(min(12, $items->count() - $limit))]) }}</button>@endif
    <a class="u-back" href="{{ $o->url() }}">→ {{ __('site.university.profile_of', ['name' => $o->name]) }}</a>
  </div>
</div>
