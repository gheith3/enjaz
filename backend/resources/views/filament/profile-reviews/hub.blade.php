<x-filament-panels::page>
    @php $s = $this->summary(); @endphp
    <div class="rh-big">
        <a class="rh-tile" href="{{ \App\Filament\Resources\ProfileReviews\ProfileReviewResource::getUrl('fields') }}">
            <span class="rh-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.6 3.3-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><circle cx="17" cy="9" r="2.5"/><path d="M16.5 14.6c2.6.2 4.4 1.9 5 5.4"/></svg></span>
            <span class="rh-name">المنجزون</span>
            <span class="rh-num">{{ $s['total'] }} <small>منجز</small></span>
            @include('filament.profile-reviews._legend', ['counts' => $s['counts'], 'total' => $s['total']])
        </a>
        <span class="rh-tile is-soon" aria-disabled="true">
            <span class="rh-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M9 11h.01M15 11h.01"/></svg></span>
            <span class="rh-name">المؤسسات</span>
            <span class="rh-num">—</span>
            <span class="rh-note">مراجعة صفحات المؤسسات: المرحلة التالية</span>
        </span>
    </div>
</x-filament-panels::page>
