{{-- An image chosen by the achiever: uploaded at once (review.js), its stored path kept in the hidden input. --}}
<div class="rv-upload{{ $path ? ' has-file' : '' }}" data-upload>
    <input type="hidden" name="{{ $name }}" value="{{ $path }}" data-upload-path>
    <label class="rv-upload__drop">
        <input type="file" accept="image/jpeg,image/png,image/webp" class="rv-sr" data-upload-file>
        <span class="rv-upload__preview" data-upload-preview>
            @if ($path)
                <img src="{{ \App\Support\ProfileReviews\ProfileReviewApplier::disk()->url($path) }}" alt="">
            @endif
        </span>
        <span class="rv-upload__cta">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/></svg>
            <span data-upload-label data-empty="{{ __('site.review.choose_image') }}" data-replace="{{ __('site.review.replace_image') }}">{{ $path ? __('site.review.replace_image') : __('site.review.choose_image') }}</span>
        </span>
    </label>
    <div class="rv-upload__bar" hidden data-upload-bar><span></span></div>
    <p class="rv-upload__status" data-upload-status aria-live="polite"
       data-uploading="{{ __('site.review.uploading') }}" data-done="{{ __('site.review.uploaded') }}" data-failed="{{ __('site.review.upload_failed') }}">{{ $path ? __('site.review.uploaded') : '' }}</p>
</div>
