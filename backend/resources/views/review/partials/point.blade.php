{{-- One point: what is written now and «تعديل»; the corrected value opens below it. --}}
@php
    $key = $item['key'];
    $type = $item['type'];
    $old = $item['old'] ?? '';
    $editing = ($item['decision'] ?? 'ok') === 'edit';
    $value = old("v.$key", $item['new'] ?? null) ?? ($type === 'image' ? null : $old);
    $empty = blank($old);
    // Photos: the site's current photos are never shown here; the achiever sends every photo themselves.
    $button = $type === 'image' ? __('site.review.send_photo') : ($empty ? __('site.review.add') : __('site.review.edit'));
    $hasError = $errors->has("v.$key");
@endphp
<div class="pt{{ $editing ? ' is-editing is-edited' : '' }}{{ $hasError ? ' is-error' : '' }}" data-pt id="pt-{{ $key }}">
    <div class="pt-row">
        <div class="pt-body">
            <p class="lbl">{{ $item['label'] }} <span class="tag">{{ __('site.review.edited') }}</span></p>
            @if ($type === 'image')
                <p class="none">{{ __('site.review.photo_wanted') }}</p>
            @elseif (in_array($type, ['select', 'scope', 'when'], true))
                @if (filled($item['shown'] ?? null))<p class="val">{{ $item['shown'] }}</p>@else<p class="none">{{ __('site.review.empty') }}</p>@endif
            @elseif ($empty)
                <p class="none">{{ __('site.review.empty') }}</p>
            @else
                <p class="val">{!! nl2br(e($old), false) !!}</p>
            @endif
        </div>
        <button type="button" class="edit" data-edit>{{ $button }}</button>
    </div>
    <div class="pt-edit">
        @switch($type)
            @case('text')
                <input type="text" name="v[{{ $key }}]" value="{{ $value }}" maxlength="1000" aria-label="{{ $item['label'] }}">
                @break
            @case('select')
                <select name="v[{{ $key }}]" aria-label="{{ $item['label'] }}">
                    @foreach ($item['options'] ?? [] as $id => $label)
                        <option value="{{ $id }}" @selected((string) $value === (string) $id)>{{ $label }}</option>
                    @endforeach
                </select>
                @break
            @case('scope')
                @include('review.partials.scope', ['name' => "v[$key]", 'value' => $value])
                @break
            @case('when')
                @include('review.partials.when', ['name' => "w[$key]", 'parts' => explode('-', (string) (($item['new'] ?? null) ?: $old))])
                @break
            @case('image')
                @include('review.partials.pick', ['name' => "v[$key]", 'path' => $item['new'] ?? null])
                @break
            @default
                <textarea name="v[{{ $key }}]" rows="{{ mb_strlen((string) $old) > 320 ? 10 : 5 }}" aria-label="{{ $item['label'] }}" data-words>{{ $value }}</textarea>
                @if ($item['min_words'] ?? null)<p class="count" data-min="{{ $item['min_words'] }}"></p>@endif
        @endswitch
        @if (filled($item['hint'] ?? null))<p class="hint">{{ $item['hint'] }}</p>@endif
        <input type="hidden" name="d[{{ $key }}]" value="{{ $editing ? 'edit' : 'ok' }}" data-decision>
        <button type="button" class="cancel" data-cancel>{{ __('site.review.undo') }}</button>
    </div>
</div>
