{{--
    The achiever's review page, design «ملف توثيق» (تصميم المراجعة ٢٠٢٠ / تصميم ٢ - معتمد). Its own quiet look, never the
    profile's design: it must not reveal how the profile will look before the launch.
--}}
@php
    $digits = fn ($n) => \App\Support\ArabicDate::digits($n);
    $journey = $review->journey['new'] ?? [];
    $socials = $review->socials['new'] ?? [];
    $videos = $review->videos['new'] ?? [];
    $contact = $review->contact['new'] ?? [];
    $firstAchievement = $sections->keys()->first(fn ($key) => str_starts_with($key, 'achievement_'));
    $n = 0;
    $socialIcons = [
        'x' => '<path d="M4 4l16 16M20 4 4 20M4 4h4l12 16h-4Z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none"/>',
        'linkedin' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 10v6M8 7.5v.2M12 16v-6m0 2.5c0-3.2 4-3.4 4 0V16"/>',
        'facebook' => '<path d="M14 21v-8h3l.5-3H14V8.5c0-.9.4-1.5 1.6-1.5H18V4.3A13 13 0 0 0 15.5 4C12.8 4 11 5.6 11 8.4V10H8v3h3v8"/>',
        'snapchat' => '<path d="M12 3c3 0 5 2.2 5 5.2v2.3l1.8.6c.5.2.5.8 0 1l-1.9.8c.6 1.7 1.8 2.8 3.3 3.3-.6.8-1.8 1-2.8 1.1-.3.9-.6 1.4-1.4 1.2-1.2-.3-2.3.6-4 .6s-2.8-.9-4-.6c-.8.2-1.1-.3-1.4-1.2-1-.1-2.2-.3-2.8-1.1 1.5-.5 2.7-1.6 3.3-3.3l-1.9-.8c-.5-.2-.5-.8 0-1l1.8-.6V8.2C7 5.2 9 3 12 3Z"/>',
    ];
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>@include('review.partials.head')</head>
<body class="rv">
<main class="wrap" id="main">
    <section class="hello">
        <div class="hello-tab">{{ __('site.review.file_tab') }}</div>
        <div class="hello-body">
            <p class="hello-kicker">{{ $achiever->isFemale() ? __('site.review.file_f') : __('site.review.file_m') }}</p>
            <h1>{{ $achiever->getTranslation('name', 'ar', false) }}</h1>
            <p>{!! __('site.review.intro_html') !!}</p>
            <p class="launch">{{ __('site.review.launch') }}</p>
            @if ($review->expires_at)<p class="valid">{{ __('site.review.valid_until', ['date' => \App\Support\ArabicDate::format($review->expires_at)]) }}</p>@endif
        </div>
    </section>

    <form id="f" method="post" action="{{ route('profile-review.save', $review->token) }}" data-upload-url="{{ route('profile-review.upload', $review->token) }}" novalidate>
        @csrf

        @if ($review->status === 'returned' && filled($review->admin_note))
            <div class="note note--team"><strong>{{ __('site.review.returned_title') }}</strong><p>{!! nl2br(e($review->admin_note), false) !!}</p></div>
        @endif
        @if ($errors->any())
            <div class="note note--error" role="alert"><strong>{{ __('site.review.errors_title') }}</strong>
                <ul>@foreach (collect($errors->all())->unique()->take(12) as $message)<li>{{ $message }}</li>@endforeach</ul></div>
        @elseif (session('review_saved'))
            <div class="note note--saved" role="status">{{ __('site.review.saved') }}</div>
        @endif

        @foreach ($sections as $key => $items)
            @if ($key === $firstAchievement)<h2 class="group">{{ __('site.review.achievements_heading') }}</h2>@endif
            @php $isAchievement = str_starts_with($key, 'achievement_'); if ($isAchievement) { $n++; } @endphp
            <section class="card{{ $isAchievement ? ' card-ach' : '' }}">
                @if ($isAchievement)
                    @php $scopeItem = $items->firstWhere('attr', 'scope_id'); $whenItem = $items->firstWhere('attr', 'achieved_at'); @endphp
                    <span class="stamp" aria-hidden="true"><b>{{ $scopeItem['shown'] ?? '' }}</b><small>{{ \Illuminate\Support\Str::afterLast($whenItem['shown'] ?? '', ' ') }}</small></span>
                @endif
                <h3>@if ($isAchievement)<span class="fileno">{{ __('site.review.achievement_n', ['n' => $digits($n)]) }}</span>@endif{{ $items->first()['title'] }}</h3>
                @foreach ($items as $item)
                    @include('review.partials.point', ['item' => $item])
                @endforeach
            </section>
        @endforeach

        <section class="card card-add">
            <h3>{{ __('site.review.additions_title') }}</h3>
            <div class="intro"><p>{{ __('site.review.additions_text') }}</p></div>
            <div class="rows" data-list="ach">
                @foreach ($review->additions ?? [] as $addition)
                    @include('review.partials.addition', ['uid' => $addition['uid'] ?: 'a'.$loop->index, 'addition' => $addition])
                @endforeach
            </div>
            <button type="button" class="add-btn" data-add="ach">+ {{ __('site.review.add_achievement') }}</button>
        </section>
        <div class="sep" aria-hidden="true"></div>

        <section class="card">
            <h3>{{ __('site.review.journey_title') }}</h3>
            <div class="intro">
                <p>{!! __('site.review.journey_text_html', ['n' => $digits(\App\Support\ProfileReviews\ProfileReviewItems::MAX_STATIONS), 'w' => $digits(\App\Support\ProfileReviews\ProfileReviewItems::STATION_WORDS)]) !!}</p>
                <p class="ex">{{ __('site.review.journey_examples') }}</p>
            </div>
            <div class="rows" data-list="station" data-max="{{ \App\Support\ProfileReviews\ProfileReviewItems::MAX_STATIONS }}">
                @foreach ($journey as $station)
                    @include('review.partials.station', ['uid' => 's'.$loop->index, 'station' => $station])
                @endforeach
            </div>
            <button type="button" class="add-btn" data-add="station">+ {{ __('site.review.add_station') }}</button>
            <p class="limit" data-limit hidden>{{ __('site.review.station_limit', ['n' => $digits(\App\Support\ProfileReviews\ProfileReviewItems::MAX_STATIONS)]) }}</p>
        </section>

        <section class="card">
            <h3>{{ __('site.review.socials_title') }}</h3>
            <div class="intro"><p>{{ __('site.review.socials_text') }}</p></div>
            @foreach (\App\Support\ProfileReviews\ProfileReviewItems::SOCIALS as $platform => $label)
                <label class="social"><span class="ic"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $socialIcons[$platform] !!}</svg></span><span class="sn">{{ $label }}</span>
                    <input type="text" dir="ltr" name="social[{{ $platform }}]" value="{{ $socials[$platform] ?? '' }}" placeholder="{{ __('site.review.social_ph') }}" maxlength="250"></label>
            @endforeach
        </section>

        <section class="card">
            <h3>{{ __('site.review.gallery_title') }}</h3>
            <div class="intro">
                <p>{!! __('site.review.gallery_text_html') !!}</p>
                <p class="ex">{{ __('site.review.gallery_examples') }}</p>
            </div>
            <div class="gallery" data-list="photo">
                @foreach ($review->gallery ?? [] as $photo)
                    @include('review.partials.photo', ['uid' => 'g'.$loop->index, 'path' => $photo['path'], 'caption' => $photo['caption'] ?? '',
                        'url' => \App\Support\ProfileReviews\ProfileReviewApplier::disk()->url($photo['path'])])
                @endforeach
            </div>
            <label class="add-btn add-photo"><input type="file" accept="image/jpeg,image/png,image/webp" multiple data-gallery>+ {{ __('site.review.add_photo') }}</label>
            <p class="limit">{{ __('site.review.add_photo_hint') }}</p>
        </section>

        <section class="card">
            <h3>{{ __('site.review.videos_title') }}</h3>
            <div class="intro"><p>{{ __('site.review.videos_text') }}</p></div>
            <div class="rows" data-list="video">
                @foreach ($videos as $video)
                    @include('review.partials.video', ['uid' => 'v'.$loop->index, 'video' => $video])
                @endforeach
            </div>
            <button type="button" class="add-btn" data-add="video">+ {{ __('site.review.add_video') }}</button>
        </section>

        <section class="card">
            <h3>{{ __('site.review.contact_title') }}</h3>
            <div class="intro"><p>{{ __('site.review.contact_text') }}</p></div>
            <label class="field">{{ __('site.review.phone') }}<input type="text" dir="ltr" name="phone" value="{{ $contact['phone'] ?? '' }}" placeholder="+968" maxlength="40"></label>
            <label class="field">{{ __('site.review.email') }}<input type="text" dir="ltr" name="email" value="{{ $contact['email'] ?? '' }}" placeholder="name@example.com" maxlength="190"></label>
        </section>

        <div class="send">
            <span class="seal" aria-hidden="true">{{ __('site.review.seal') }}</span>
            <p>{!! __('site.review.send_text_html') !!}</p>
            <button type="submit" name="action" value="submit" class="btn btn-main">{{ __('site.review.submit') }}</button><br>
            <button type="submit" name="action" value="draft" class="btn btn-soft">{{ __('site.review.save') }}</button>
        </div>
    </form>
</main>

<template id="tpl-station">@include('review.partials.station', ['uid' => '__UID__', 'station' => []])</template>
<template id="tpl-ach">@include('review.partials.addition', ['uid' => '__UID__', 'addition' => []])</template>
<template id="tpl-video">@include('review.partials.video', ['uid' => '__UID__', 'video' => []])</template>
<template id="tpl-photo">@include('review.partials.photo', ['uid' => '__UID__', 'path' => '', 'caption' => '', 'url' => ''])</template>
</body>
</html>
