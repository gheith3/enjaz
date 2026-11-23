{{-- An achievement the achiever adds. $uid keys the inputs; the <template> copy uses __UID__. --}}
@php
    $a = $addition ?? [];
    $base = "add[$uid]";
    $whenParts = array_pad(explode('-', (string) ($a['when'] ?? '')), 3, '');
@endphp
<article class="rv-add" data-addition>
    <header class="rv-add__head">
        <h3><span class="rv-add__plus" aria-hidden="true">+</span>{{ __('site.review.new_achievement') }}</h3>
        <button type="button" class="rv-link-btn" data-addition-remove>{{ __('site.review.remove') }}</button>
    </header>
    <div class="rv-add__grid">
        <label class="rv-field rv-field--wide"><span>{{ __('site.review.f_title') }}</span>
            <input type="text" class="rv-input" name="{{ $base }}[title]" value="{{ $a['title'] ?? '' }}" maxlength="255" data-addition-title></label>
        <div class="rv-field rv-field--wide"><span>{{ __('site.review.f_when') }}</span>
            @include('review.partials.when', ['name' => $base, 'parts' => $whenParts])</div>
        <label class="rv-field"><span>{{ __('site.review.f_scope') }}</span>
            <select class="rv-input" name="{{ $base }}[scope_id]">
                <option value="">{{ __('site.review.choose') }}</option>
                @foreach ($scopes as $id => $label)
                    <option value="{{ $id }}" @selected((string) ($a['scope_id'] ?? '') === (string) $id)>{{ $label }}</option>
                @endforeach
            </select></label>
        <label class="rv-field"><span>{{ __('site.review.f_discipline') }}</span>
            <input type="text" class="rv-input" name="{{ $base }}[discipline]" value="{{ $a['discipline'] ?? '' }}" maxlength="255"></label>
        <label class="rv-field"><span>{{ __('site.review.f_issuer') }}</span>
            <input type="text" class="rv-input" name="{{ $base }}[issuer]" value="{{ $a['issuer'] ?? '' }}" maxlength="255"></label>
        <label class="rv-field"><span>{{ __('site.review.f_country') }}</span>
            <input type="text" class="rv-input" name="{{ $base }}[country]" value="{{ $a['country'] ?? '' }}" maxlength="255"></label>
        <label class="rv-field rv-field--wide"><span>{{ __('site.review.f_description') }}</span>
            <textarea class="rv-input rv-input--area" name="{{ $base }}[description]" rows="4" data-autosize>{{ $a['description'] ?? '' }}</textarea></label>
        <div class="rv-field rv-field--wide"><span>{{ __('site.review.f_cover') }} <small>{{ __('site.review.optional') }}</small></span>
            @include('review.partials.image', ['name' => "{$base}[cover]", 'path' => $a['cover'] ?? null])</div>
    </div>
</article>
