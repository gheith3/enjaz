{{-- The review link once it can no longer be edited: sent, approved or expired. --}}
@php
    $state = match (true) {
        in_array($review->status, ['approved', 'closed'], true) => 'approved',
        $review->status === 'submitted' => 'submitted',
        default => 'expired',
    };
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>@include('review.partials.head')</head>
<body class="rv rv--closed">
<main class="rv-closed">
    <div class="rv-closed__card">
        <span class="rv-closed__mark rv-closed__mark--{{ $state }}" aria-hidden="true">
            @if ($state === 'expired')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            @else
                <svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
            @endif
        </span>
        <p class="rv-closed__brand">{{ __('site.review.brand') }}</p>
        <h1>{{ __("site.review.closed_{$state}_title") }}</h1>
        <p>{{ __("site.review.closed_{$state}_text") }}</p>
    </div>
</main>
</body>
</html>
