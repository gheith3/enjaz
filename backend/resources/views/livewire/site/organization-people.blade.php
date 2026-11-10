<section class="section wrap" aria-label="{{ __('site.university.people_of', ['name' => $organization->name]) }}">
  <div class="orgs-chips up-chips">
    @foreach (['' => 'all', 'staff' => 'kind_filter_staff', 'student' => 'kind_filter_student'] as $k => $label)
      <button type="button" @class(['chip', 'active' => $kind === $k]) aria-pressed="{{ $kind === $k ? 'true' : 'false' }}" wire:click="setKind('{{ $k }}')"><span>{{ __('site.university.'.$label) }}</span></button>
    @endforeach
    @foreach (['m', 'f'] as $g)
      <button type="button" @class(['chip', 'up-g', 'active' => $gender === $g]) aria-pressed="{{ $gender === $g ? 'true' : 'false' }}" wire:click="toggleGender('{{ $g }}')"><span>{{ __('site.university.gender_'.$g) }}</span></button>
    @endforeach
  </div>
  <p class="ua-count" aria-live="polite">{{ \App\Support\ArabicDate::digits($items->count()) }} {{ $items->count() > 2 && $items->count() <= 10 ? __('site.university.achievers_few') : __('site.university.achievers_many') }}</p>
  <div class="grid-2 cards-ov">
    @forelse ($items as $person)
      <x-site.cards.person :achiever="$person" wire:key="up-{{ $person->id }}" :cat="\App\Models\Organization::affiliationLabel($person->pivot->kind, $person->gender)" :headline="$person->pivot->roleLabel() ?: $person->headline" />
    @empty
      <p class="u-soon">{{ __('site.university.no_people_match') }}</p>
    @endforelse
  </div>
  <a class="u-back" href="{{ $organization->url() }}">→ {{ __('site.university.profile_of', ['name' => $organization->name]) }}</a>
</section>
