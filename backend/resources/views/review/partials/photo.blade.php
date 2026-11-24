{{-- One photo of «المكتبة المرئية»: the photo on the right, its title on the left. --}}
<div class="g-item" data-row>
    <img src="{{ $url ?? '' }}" alt="" data-photo>
    <textarea name="gal[{{ $uid }}][caption]" rows="3" maxlength="500" placeholder="{{ __('site.review.photo_caption_ph') }}">{{ $caption ?? '' }}</textarea>
    <input type="hidden" name="gal[{{ $uid }}][path]" value="{{ $path ?? '' }}" data-path>
    <button type="button" class="x" data-remove-row aria-label="{{ __('site.review.remove') }}">×</button>
</div>
