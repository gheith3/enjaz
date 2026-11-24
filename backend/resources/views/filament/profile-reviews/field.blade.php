<x-filament-panels::page>
    @php
        $people = $this->people();
        $counts = \App\Support\ProfileReviews\ReviewBoard::counts($people);
        $shown = $filter === 'all' ? $people : $people->where('stage', $filter);
        $actions = ['pending' => 'فتح رابط المراجعة', 'reviewed' => 'مراجعة التعديلات واعتمادها', 'published' => 'عرض ما نُشر'];
    @endphp
    <div class="rh-filters">
        <button type="button" class="{{ $filter === 'all' ? 'on' : '' }}" wire:click="$set('filter', 'all')">الكل <b>{{ $people->count() }}</b></button>
        @foreach (\App\Support\ProfileReviews\ReviewBoard::STAGES as $stage => $label)
            <button type="button" class="{{ $filter === $stage ? 'on' : '' }}" wire:click="$set('filter', '{{ $stage }}')"><i class="rh-dot rh-{{ $stage }}"></i>{{ $label }} <b>{{ $counts[$stage] }}</b></button>
        @endforeach
    </div>
    @if ($shown->isEmpty())
        <p class="prv-muted">لا يوجد منجزون هنا.</p>
    @else
        <div class="rh-people">
            @foreach ($shown as $p)
                <button type="button" class="rh-person s-{{ $p['stage'] }}" wire:click="open({{ $p['achiever']->id }})" wire:key="ach-{{ $p['achiever']->id }}">
                    <span class="rh-badge">{{ \App\Support\ProfileReviews\ReviewBoard::STAGES[$p['stage']] }}</span>
                    <span class="rh-pname">{{ $p['achiever']->getTranslation('name', 'ar', false) }}</span>
                    <span class="rh-phead">{{ $p['achiever']->getTranslation('headline', 'ar', false) ?: '—' }}</span>
                    <span class="rh-act">{{ $p['review'] ? $actions[$p['stage']] : 'إنشاء رابط المراجعة' }}</span>
                </button>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
