{{-- One photo: uploaded at once by review.js, its stored path kept in the hidden input. --}}
<label class="pick" data-pick>
    <input type="file" accept="image/jpeg,image/png,image/webp" data-file>
    @if (filled($path ?? null))<img src="{{ \App\Support\ProfileReviews\ProfileReviewApplier::disk()->url($path) }}" alt="">@endif
    <span data-pick-label data-empty="{{ __('site.review.choose_image') }}" data-done="{{ __('site.review.image_chosen') }}" data-busy="{{ __('site.review.uploading') }}" data-failed="{{ __('site.review.upload_failed') }}">{{ filled($path ?? null) ? __('site.review.image_chosen') : __('site.review.choose_image') }}</span>
</label>
<input type="hidden" name="{{ $name }}" value="{{ $path ?? '' }}" data-path>
