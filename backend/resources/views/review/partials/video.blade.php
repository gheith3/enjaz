{{-- One interview: title and link. --}}
<div class="row video" data-row>
    <div class="grow">
        <input type="text" name="vid[{{ $uid }}][title]" value="{{ $video['title'] ?? '' }}" placeholder="{{ __('site.review.video_title_ph') }}" maxlength="255">
        <input type="text" dir="ltr" name="vid[{{ $uid }}][url]" value="{{ $video['url'] ?? '' }}" placeholder="https://" maxlength="500">
    </div>
    <button type="button" class="x" data-remove-row aria-label="{{ __('site.review.remove') }}">×</button>
</div>
