{{-- Year (required), month and day (optional). $parts = [year, month, day]; $day = false shows the year only. --}}
@php [$y, $m, $d] = array_pad(array_map('intval', $parts ?? []), 3, 0); $day = $day ?? true; $yearOnly = $yearOnly ?? false; @endphp
<div class="when{{ $yearOnly ? ' when1' : '' }}">
    <select name="{{ $name }}[y]" aria-label="{{ __('site.review.year') }}">
        <option value="">{{ __('site.review.year') }}</option>
        @for ($year = (int) date('Y'); $year >= 1950; $year--)
            <option value="{{ $year }}" @selected($y === $year)>{{ $year }}</option>
        @endfor
    </select>
    @unless ($yearOnly)
        <select name="{{ $name }}[m]" aria-label="{{ __('site.review.month') }}">
            <option value="">{{ __('site.review.month') }} ({{ __('site.review.optional') }})</option>
            @foreach (__('site.review.months') as $index => $month)
                <option value="{{ $index + 1 }}" @selected($m === $index + 1)>{{ $month }}</option>
            @endforeach
        </select>
        <select name="{{ $name }}[d]" aria-label="{{ __('site.review.day') }}">
            <option value="">{{ __('site.review.day') }} ({{ __('site.review.optional') }})</option>
            @for ($i = 1; $i <= 31; $i++)
                <option value="{{ $i }}" @selected($d === $i)>{{ $i }}</option>
            @endfor
        </select>
    @endunless
</div>
