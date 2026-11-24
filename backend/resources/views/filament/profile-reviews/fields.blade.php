<x-filament-panels::page>
    <div class="rh-fields">
        @foreach ($this->fields() as $field)
            @php $total = $field['people']->count(); @endphp
            <a class="rh-tile{{ $total ? '' : ' is-empty' }}" href="{{ \App\Filament\Resources\ProfileReviews\ProfileReviewResource::getUrl('field', ['field' => $field['key']]) }}">
                <span class="rh-name">{{ $field['name'] }}</span>
                <span class="rh-num">{{ $total }} <small>منجز</small></span>
                @include('filament.profile-reviews._legend', ['counts' => \App\Support\ProfileReviews\ReviewBoard::counts($field['people']), 'total' => $total])
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
