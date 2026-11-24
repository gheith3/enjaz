{{-- One station of «محطات مسيرتك»: the year and up to 8 words. --}}
<div class="row station" data-row>
    <div class="station-when">@include('review.partials.when', ['name' => "j[$uid]", 'parts' => [$station['year'] ?? ''], 'yearOnly' => true])</div>
    <div class="grow">
        <input type="text" name="j[{{ $uid }}][text]" value="{{ $station['text'] ?? '' }}" placeholder="{{ __('site.review.station_ph') }}" maxlength="200" data-words>
        <p class="count" data-max="{{ \App\Support\ProfileReviews\ProfileReviewItems::STATION_WORDS }}"></p>
    </div>
    <button type="button" class="x" data-remove-row aria-label="{{ __('site.review.remove') }}">×</button>
</div>
