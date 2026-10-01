<div>
    @section('title', $achievement->title_ar)

    <livewire:page-header :background-image="$achievement->cover_image_path" :title="$achievement->title_ar" :sub-title="$achievement->achievementMainType->title_ar" />


    <div class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @if ($achievement->organizations->count() > 0)
                        @foreach ($achievement->organizations as $organization)
                            <div class="pb-3" wire:key="{{ $organization->id }}">
                                @livewire('components.organization-card', ['organization' => $organization], key("card-{$organization->id}"))
                            </div>
                        @endforeach
                    @endif
                    @if ($achievement->publicFigures->count() > 0)
                        @foreach ($achievement->publicFigures as $achiever)
                            <div class="pb-3" wire:key="{{ $achiever->id }}">
                                @livewire('components.achiever-card', ['achiever' => $achiever], key("card-{$achiever->id}"))
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="col-md-9">
                    <div class="app-card p-3" id="statistics-card">
                        <div class="d-flex gap-2">

                            <span class="achievement-label">{{ $achievement->created_at->year }}</span>

                            <span class="achievement-label">{{ $achievement->achievementMainType->title_ar }}</span>

                            <span class="achievement-label">{{ $achievement->subAchievementType->title_ar }}</span>

                            <span class="achievement-label">{{ $achievement->achievementScope->title_ar }}</span>

                        </div>
                        <div class="story-text mt-2" id="story-text">
                            {!! $achievement->description_ar !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div>
        <livewire:new-achievements :section-title="'المزيد'" :active-achievement="$achievement->id" :achievementMainTypeId="$achievement->achievement_main_type_id" />
    </div>
    <br>
</div>

@push('styles')
    <style>
        .achievement-label {
            font-size: 12px;
            border: 1px solid #1f4a50;
            color: #1f4a50;
            border-radius: 12px;
            padding: 5px 10px;
        }
    </style>
@endpush
