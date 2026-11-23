{{--
    The achiever's review page. Deliberately its own quiet design (no profile layout, no motion): it must not
    reveal how the profile will look before the launch.
--}}
@php
    $progress = $review->progress();
    $sectionList = $sections->map(fn ($items, $key) => ['key' => $key, 'title' => $items->first()['title'], 'count' => $items->count()])->values();
    $firstAchievement = $sectionList->first(fn ($s) => str_starts_with($s['key'], 'achievement_'))['key'] ?? null;
    // First letter of the name itself, never of a title («د. جوخة» → «ج»).
    $initial = mb_substr(preg_replace('/^((د|الدكتورة?|الشيخة?|السيدة?|المهندسة?|الأستاذة?)\.?\s+)+/u', '', trim((string) $achiever->getTranslation('name', 'ar', false))), 0, 1);
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>@include('review.partials.head')</head>
<body class="rv">

<header class="rv-bar">
    <div class="rv-bar__inner">
        <span class="rv-bar__brand">{{ __('site.review.brand') }}</span>
        <span class="rv-bar__private">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>
            {{ __('site.review.private') }}
        </span>
    </div>
</header>

<section class="rv-hero">
    <div class="rv-hero__inner">
        <span class="rv-hero__monogram" aria-hidden="true">{{ $initial }}</span>
        <div>
            <p class="rv-hero__kicker">{{ __('site.review.title') }}</p>
            <h1 class="rv-hero__title">{{ __('site.review.hello', ['name' => $achiever->shortName()]) }}</h1>
            <p class="rv-hero__intro">{{ __('site.review.intro') }}</p>
            <ol class="rv-steps">
                @foreach (__('site.review.steps') as $step)
                    <li><span>{{ \App\Support\ArabicDate::digits($loop->iteration) }}</span>{{ $step }}</li>
                @endforeach
            </ol>
            @if ($review->expires_at)
                <p class="rv-hero__valid">{{ __('site.review.valid_until', ['date' => \App\Support\ArabicDate::format($review->expires_at)]) }}</p>
            @endif
        </div>
    </div>
</section>

<div class="rv-layout">
    <aside class="rv-toc" aria-label="{{ __('site.review.title') }}">
        <ol>
            @foreach ($sectionList as $section)
                <li><a href="#sec-{{ $section['key'] }}" data-toc="{{ $section['key'] }}">
                    <span class="rv-toc__dot" aria-hidden="true"></span>
                    <span class="rv-toc__title">{{ $section['title'] }}</span>
                    <span class="rv-toc__count" data-toc-count>0/{{ $section['count'] }}</span>
                </a></li>
            @endforeach
            <li><a href="#sec-additions"><span class="rv-toc__dot rv-toc__dot--plus" aria-hidden="true"></span><span class="rv-toc__title">{{ __('site.review.additions_title') }}</span></a></li>
        </ol>
    </aside>

    <form class="rv-main" method="post" action="{{ route('profile-review.save', $review->token) }}" id="rv-form" novalidate
          data-upload-url="{{ route('profile-review.upload', $review->token) }}">
        @csrf

        @if ($review->status === 'returned' && filled($review->admin_note))
            <div class="rv-callout rv-callout--note">
                <strong>{{ __('site.review.returned_title') }}</strong>
                <p>{!! nl2br(e($review->admin_note), false) !!}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rv-callout rv-callout--error" role="alert">
                <strong>{{ __('site.review.errors_title') }}</strong>
                <ul>@foreach (collect($errors->all())->unique()->take(12) as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        @elseif (session('review_saved'))
            <div class="rv-callout rv-callout--saved" role="status">{{ __('site.review.saved') }}</div>
        @endif

        @foreach ($sectionList as $index => $section)
            @if ($section['key'] === $firstAchievement)
                <h2 class="rv-group-title">{{ __('site.review.achievements_heading') }}</h2>
            @endif
            <section class="rv-sec" id="sec-{{ $section['key'] }}" data-section="{{ $section['key'] }}">
                <header class="rv-sec__head">
                    <span class="rv-sec__num" aria-hidden="true">{{ \App\Support\ArabicDate::digits(str_pad($index + 1, 2, '0', STR_PAD_LEFT)) }}</span>
                    <div class="rv-sec__titles">
                        @if (str_starts_with($section['key'], 'achievement_'))
                            <p class="rv-sec__kicker">{{ __('site.review.achievement_n', ['n' => \App\Support\ArabicDate::digits($index + 1 - $sectionList->search(fn ($s) => $s['key'] === $firstAchievement))]) }}</p>
                        @endif
                        <h2 class="rv-sec__title">{{ $section['title'] }}</h2>
                    </div>
                    <span class="rv-sec__count" data-section-count>0/{{ $section['count'] }}</span>
                </header>
                <div class="rv-sec__items">
                    @foreach ($sections[$section['key']] as $item)
                        @include('review.partials.item', ['item' => $item])
                    @endforeach
                </div>
                <button type="button" class="rv-confirm-all" data-confirm-section>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 12.5 4 4L15 8.5"/><path d="m11 16 1 1 9-9.5"/></svg>
                    {{ __('site.review.confirm_section') }}
                </button>
            </section>
        @endforeach

        <section class="rv-sec rv-sec--add" id="sec-additions">
            <header class="rv-sec__head">
                <span class="rv-sec__num rv-sec__num--plus" aria-hidden="true">+</span>
                <div class="rv-sec__titles">
                    <h2 class="rv-sec__title">{{ __('site.review.additions_title') }}</h2>
                    <p class="rv-sec__sub">{{ __('site.review.additions_text') }}</p>
                </div>
            </header>
            <div class="rv-adds" data-additions>
                @foreach ($review->additions ?? [] as $addition)
                    @include('review.partials.addition', ['uid' => $addition['uid'] ?: 'a'.$loop->index, 'addition' => $addition])
                @endforeach
            </div>
            <button type="button" class="rv-add-btn" data-addition-add>
                <span aria-hidden="true">+</span>{{ __('site.review.add_achievement') }}
            </button>
            <template id="rv-addition-template">
                @include('review.partials.addition', ['uid' => '__UID__', 'addition' => []])
            </template>
        </section>

        <section class="rv-sec rv-sec--note" id="sec-note">
            <header class="rv-sec__head">
                <span class="rv-sec__num rv-sec__num--note" aria-hidden="true">✎</span>
                <div class="rv-sec__titles">
                    <h2 class="rv-sec__title">{{ __('site.review.note_title') }}</h2>
                    <p class="rv-sec__sub">{{ __('site.review.note_text') }}</p>
                </div>
            </header>
            <textarea class="rv-input rv-input--area" name="note" rows="4" data-autosize aria-label="{{ __('site.review.note_title') }}">{{ old('note', $review->note) }}</textarea>
        </section>

        <div class="rv-dock" data-dock>
            <div class="rv-dock__inner">
                <div class="rv-dock__progress">
                    <p class="rv-dock__text" data-progress-text data-template="{{ __('site.review.progress', ['done' => '__D__', 'total' => '__T__']) }}">
                        {{ __('site.review.progress', ['done' => $progress['answered'], 'total' => $progress['total']]) }}
                    </p>
                    <div class="rv-meter" aria-hidden="true"><span data-progress-bar style="width: {{ $progress['total'] ? round($progress['answered'] / $progress['total'] * 100) : 100 }}%"></span></div>
                </div>
                <div class="rv-dock__actions">
                    <button type="submit" name="action" value="draft" class="rv-btn rv-btn--ghost" formnovalidate>{{ __('site.review.save') }}</button>
                    <button type="submit" name="action" value="submit" class="rv-btn rv-btn--primary" data-submit
                            data-remaining="{{ __('site.review.remaining', ['n' => '__N__']) }}">{{ __('site.review.submit') }}</button>
                </div>
            </div>
            <p class="rv-dock__hint" data-submit-hint>{{ __('site.review.submit_hint') }}</p>
        </div>
    </form>
</div>

</body>
</html>
