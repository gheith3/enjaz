@props(['organization'])

@php
    /** @var \App\Models\Organization $organization */
@endphp

<a href="{{ route('institutions.show', $organization) }}" {{ $attributes->merge(['class' => 'flex items-center justify-center bg-white rounded-[4.2rem] shadow-[0_0.3rem_0.6rem_rgba(0,0,0,0.16)] h-[31.6rem] max-lg:h-auto max-lg:aspect-[353/316] transition-transform hover:-translate-y-1']) }}>
  @if ($organization->logo_path)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($organization->logo_path) }}" alt="{{ $organization->t('name') }}" class="max-w-[80%]">
  @else
    <span class="text-[2.4rem] font-bold text-teal px-8 text-center">{{ $organization->t('name') }}</span>
  @endif
</a>
