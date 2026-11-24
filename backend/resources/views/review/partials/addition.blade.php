{{-- An achievement the achiever adds. $uid keys the inputs; the <template> copy uses __UID__. --}}
@php $a = $addition ?? []; $base = "add[$uid]"; @endphp
<section class="card add" data-row>
    <div class="add-head"><h3>{{ __('site.review.new_achievement') }}</h3><button type="button" class="cancel" data-remove-row>{{ __('site.review.remove') }}</button></div>
    <label>{{ __('site.review.f_title') }}<input type="text" name="{{ $base }}[title]" value="{{ $a['title'] ?? '' }}" maxlength="255"></label>
    <div class="lab">{{ __('site.review.f_when') }}@include('review.partials.when', ['name' => $base, 'parts' => explode('-', (string) ($a['when'] ?? ''))])</div>
    <div class="lab">{{ __('site.review.f_scope') }}@include('review.partials.scope', ['name' => "{$base}[scope_id]", 'value' => $a['scope_id'] ?? null])</div>
    <label>{{ __('site.review.f_discipline') }}<input type="text" name="{{ $base }}[discipline]" value="{{ $a['discipline'] ?? '' }}" maxlength="255"></label>
    <label>{{ __('site.review.f_country') }}<input type="text" name="{{ $base }}[country]" value="{{ $a['country'] ?? '' }}" maxlength="255"></label>
    <label>{{ __('site.review.f_description') }}<textarea name="{{ $base }}[description]" rows="6" data-words placeholder="{{ __('site.review.f_description_ph') }}">{{ $a['description'] ?? '' }}</textarea><p class="count" data-min="{{ \App\Support\ProfileReviews\ProfileReviewItems::MIN_WORDS }}"></p></label>
    <div class="lab">{{ __('site.review.f_cover') }} ({{ __('site.review.optional') }})@include('review.partials.pick', ['name' => "{$base}[cover]", 'path' => $a['cover'] ?? null])</div>
</section>
