{{-- Year (required), month and day (optional). $parts = [year, month, day]. --}}
@php [$y, $m, $d] = array_pad(array_map('intval', $parts ?? []), 3, 0); @endphp
<div class="rv-when">
    <label><span>{{ __('site.review.year') }}</span>
        <select class="rv-input" name="{{ $name }}[y]">
            <option value=""></option>
            @for ($year = (int) date('Y'); $year >= 1950; $year--)
                <option value="{{ $year }}" @selected($y === $year)>{{ $year }}</option>
            @endfor
        </select>
    </label>
    <label><span>{{ __('site.review.month') }} <small>{{ __('site.review.optional') }}</small></span>
        <select class="rv-input" name="{{ $name }}[m]">
            <option value=""></option>
            @foreach (__('site.review.months') as $index => $month)
                <option value="{{ $index + 1 }}" @selected($m === $index + 1)>{{ $month }}</option>
            @endforeach
        </select>
    </label>
    <label><span>{{ __('site.review.day') }} <small>{{ __('site.review.optional') }}</small></span>
        <select class="rv-input" name="{{ $name }}[d]">
            <option value=""></option>
            @for ($day = 1; $day <= 31; $day++)
                <option value="{{ $day }}" @selected($d === $day)>{{ $day }}</option>
            @endfor
        </select>
    </label>
</div>
