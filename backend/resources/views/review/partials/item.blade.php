{{-- One point of the profile: what is written now, «صحيحة» / «تعديل», and the corrected value. --}}
@php
    $key = $item['key'];
    $type = $item['type'];
    $old = $item['old'] ?? '';
    $decision = old("d.$key", $item['decision'] ?? null);
    $value = old("v.$key", $item['new'] ?? null) ?? ($type === 'image' ? null : $old);
    $hasError = $errors->has("d.$key") || $errors->has("v.$key");
    $when = $type === 'when' ? array_pad(explode('-', (string) (($item['new'] ?? null) ?: $old)), 3, '') : null;
@endphp
<article class="rv-item{{ $hasError ? ' is-error' : '' }}" data-item data-type="{{ $type }}" data-state="{{ $decision }}" id="item-{{ $key }}">
    <header class="rv-item__head">
        <h3 class="rv-item__label">{{ $item['label'] }}</h3>
        <span class="rv-item__badge" aria-hidden="true" data-ok="✓ {{ __('site.review.correct') }}" data-edit="✎ {{ __('site.review.edited') }}"></span>
    </header>

    <div class="rv-item__current">
        <span class="rv-item__current-label">{{ __('site.review.current') }}</span>
        @if ($type === 'image')
            @if ($old)
                <img src="{{ $old }}" alt="" loading="lazy" class="rv-item__img">
            @else
                <p class="rv-item__empty">{{ __('site.review.no_image') }}</p>
            @endif
        @elseif (in_array($type, ['select', 'when'], true))
            @if (filled($item['shown'] ?? null))
                <p class="rv-item__text">{{ $item['shown'] }}</p>
            @else
                <p class="rv-item__empty">{{ __('site.review.empty') }}</p>
            @endif
        @elseif (filled($old))
            <p class="rv-item__text">{!! nl2br(e($old), false) !!}</p>
        @else
            <p class="rv-item__empty">{{ __('site.review.empty') }}</p>
        @endif
    </div>

    <fieldset class="rv-choice">
        <legend class="rv-sr">{{ $item['label'] }}</legend>
        <label class="rv-choice__opt rv-choice__opt--ok">
            <input type="radio" name="d[{{ $key }}]" value="ok" @checked($decision === 'ok')>
            <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>{{ __('site.review.correct') }}</span>
        </label>
        <label class="rv-choice__opt rv-choice__opt--edit">
            <input type="radio" name="d[{{ $key }}]" value="edit" @checked($decision === 'edit')>
            <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/></svg>{{ __('site.review.edit') }}</span>
        </label>
    </fieldset>

    <div class="rv-edit">
        <span class="rv-edit__label">{{ __('site.review.your_version') }}</span>
        @switch($type)
            @case('text')
                <input type="text" class="rv-input" name="v[{{ $key }}]" value="{{ $value }}" maxlength="1000" aria-label="{{ $item['label'] }}">
                @break
            @case('select')
                <select class="rv-input" name="v[{{ $key }}]" aria-label="{{ $item['label'] }}">
                    <option value="">{{ __('site.review.choose') }}</option>
                    @foreach ($item['options'] ?? [] as $id => $label)
                        <option value="{{ $id }}" @selected((string) $value === (string) $id)>{{ $label }}</option>
                    @endforeach
                </select>
                @break
            @case('when')
                @include('review.partials.when', ['name' => "w[$key]", 'parts' => $when])
                @break
            @case('image')
                @include('review.partials.image', ['name' => "v[$key]", 'path' => $value])
                @break
            @default
                <textarea class="rv-input rv-input--area" name="v[{{ $key }}]" rows="{{ in_array($type, ['html', 'textarea'], true) && mb_strlen((string) $old) > 300 ? 8 : 4 }}"
                    dir="{{ $type === 'links' ? 'ltr' : 'rtl' }}" aria-label="{{ $item['label'] }}" data-autosize>{{ $value }}</textarea>
        @endswitch
        @if (filled($item['hint'] ?? null))
            <p class="rv-hint">{{ $item['hint'] }}</p>
        @endif
    </div>
</article>
