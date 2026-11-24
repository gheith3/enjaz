<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ProfileReview;
use App\Support\ProfileReviews\ProfileReviewApplier;
use App\Support\ProfileReviews\ProfileReviewItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * The achiever's private review page (/review/{token}), design «ملف توثيق»: Arabic, its own look (never the profile's
 * design, nothing of the launch is revealed). The achiever reads every point and presses «تعديل» on what is wrong;
 * what they leave counts as correct. Answers are kept on the review; nothing reaches the site before the team approves.
 */
class ProfileReviewController extends Controller
{
    public function show(string $token): Response
    {
        app()->setLocale('ar');
        $review = $this->review($token);

        if ($review->isEditable() && $review->status === 'sent') {
            $review->update(['status' => 'opened', 'opened_at' => now()]);
        }

        $view = $review->isEditable() ? 'review.page' : 'review.closed';

        return response()->view($view, [
            'review' => $review,
            'achiever' => $review->achiever,
            'sections' => collect($review->items)->groupBy('section'),
            'scopes' => ProfileReviewItems::scopeOptions(),
        ])->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'no-store, private');
    }

    public function save(Request $request, string $token): RedirectResponse
    {
        app()->setLocale('ar');
        $review = $this->review($token);
        abort_unless($review->isEditable(), 403);

        $request->validate([
            'd' => ['array'], 'd.*' => ['nullable', 'in:ok,edit'],
            'v' => ['array'], 'v.*' => ['nullable', 'string', 'max:30000'],
            'w' => ['array'], 'w.*.*' => ['nullable', 'integer', 'min:0', 'max:2100'],
            'j' => ['array', 'max:'.ProfileReviewItems::MAX_STATIONS],
            'j.*.y' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'j.*.text' => ['nullable', 'string', 'max:200'],
            'social' => ['array'], 'social.*' => ['nullable', 'string', 'max:250'],
            'add' => ['array', 'max:40'],
            'add.*.title' => ['nullable', 'string', 'max:255'],
            'add.*.description' => ['nullable', 'string', 'max:20000'],
            'add.*.discipline' => ['nullable', 'string', 'max:255'],
            'add.*.country' => ['nullable', 'string', 'max:255'],
            'add.*.scope_id' => ['nullable', 'integer'],
            'add.*.cover' => ['nullable', 'string', 'max:255'],
            'add.*.y' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'add.*.m' => ['nullable', 'integer', 'min:1', 'max:12'],
            'add.*.d' => ['nullable', 'integer', 'min:1', 'max:31'],
            'gal' => ['array', 'max:200'],
            'gal.*.path' => ['nullable', 'string', 'max:255'],
            'gal.*.caption' => ['nullable', 'string', 'max:500'],
            'vid' => ['array', 'max:50'],
            'vid.*.title' => ['nullable', 'string', 'max:255'],
            'vid.*.url' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'string', 'max:190'],
        ]);

        $decisions = $request->input('d', []);
        $items = collect($review->items)->map(function (array $item) use ($request, $decisions, $review) {
            $key = $item['key'];
            $item['decision'] = ($decisions[$key] ?? 'ok') === 'edit' ? 'edit' : 'ok';
            $item['new'] = $item['decision'] !== 'edit' ? null : match ($item['type']) {
                'when' => self::when($request->input("w.$key", [])),
                'image' => $this->ownUpload($review, $request->input("v.$key")),
                default => $request->input("v.$key"),
            };
            if ($item['decision'] === 'edit' && $item['type'] === 'image' && blank($item['new'])) {
                $item['decision'] = 'ok';   // «تغيير» pressed but no photo chosen: nothing to change
            }
            $item['approved'] = null;

            return $item;
        })->all();

        $journey = collect($request->input('j', []))->map(fn ($station) => [
            'year' => (string) ((int) ($station['y'] ?? 0) ?: ''),
            'text' => trim((string) ($station['text'] ?? '')),
        ])->filter(fn ($station) => $station['text'] !== '')->take(ProfileReviewItems::MAX_STATIONS)->values()->all();

        $socials = collect(ProfileReviewItems::SOCIALS)
            ->map(fn ($label, $platform) => trim((string) $request->input("social.$platform", '')))->all();

        $videos = collect($request->input('vid', []))->map(fn ($v) => [
            'title' => trim((string) ($v['title'] ?? '')),
            'url' => trim((string) ($v['url'] ?? '')),
        ])->filter(fn ($v) => $v['url'] !== '')->values()->all();

        $gallery = collect($request->input('gal', []))->map(fn ($photo) => [
            'path' => $this->ownUpload($review, $photo['path'] ?? null),
            'caption' => trim((string) ($photo['caption'] ?? '')),
            'approved' => null,
        ])->filter(fn ($photo) => filled($photo['path']))->values()->all();

        $additions = collect($request->input('add', []))->map(fn (array $add, $uid) => [
            'uid' => (string) Str::of((string) $uid)->replaceMatches('/[^A-Za-z0-9_]/', '')->limit(20, ''),
            'title' => trim((string) ($add['title'] ?? '')),
            'when' => self::when($add),
            'scope_id' => $add['scope_id'] ?? null,
            'discipline' => $add['discipline'] ?? null,
            'country' => $add['country'] ?? null,
            'description' => $add['description'] ?? null,
            'cover' => $this->ownUpload($review, $add['cover'] ?? null),
            'approved' => null,
        ])->filter(fn ($add) => filled($add['title']) || filled($add['description']))->values()->all();

        // Answers are kept even when the submit is refused, so nothing the achiever wrote is lost.
        $review->update([
            'items' => $items,
            'additions' => $additions,
            'journey' => [...($review->journey ?? []), 'new' => $journey, 'approved' => null],
            'socials' => [...($review->socials ?? []), 'new' => $socials, 'approved' => null],
            'videos' => [...($review->videos ?? []), 'new' => $videos, 'approved' => null],
            'contact' => [...($review->contact ?? []), 'new' => ['phone' => trim((string) $request->input('phone')), 'email' => trim((string) $request->input('email'))], 'approved' => null],
            'gallery' => $gallery,
        ]);

        if ($request->input('action') === 'submit') {
            $this->assertComplete($items, $additions, $journey);
            $review->update(['status' => 'submitted', 'submitted_at' => now()]);
        }

        return redirect()->to($review->url().($review->status === 'submitted' ? '' : '#saved'))
            ->with('review_saved', $review->status !== 'submitted');
    }

    /** One image, stored at once on the media disk (the page uploads each image as soon as it is chosen). */
    public function upload(Request $request, string $token): JsonResponse
    {
        $review = $this->review($token);
        abort_unless($review->isEditable(), 403);

        $request->validate(['image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:9800']]);

        $path = $request->file('image')->store("profile-reviews/{$review->id}", ProfileReviewApplier::diskName());

        return response()->json(['path' => $path, 'url' => ProfileReviewApplier::disk()->url($path)]);
    }

    private function review(string $token): ProfileReview
    {
        abort_unless(strlen($token) === 48 && ctype_alnum($token), 404);

        return ProfileReview::query()->where('token', $token)->with('achiever')->firstOrFail();
    }

    /** An uploaded path is accepted only from this review's own folder. */
    private function ownUpload(ProfileReview $review, mixed $path): ?string
    {
        return is_string($path) && Str::startsWith($path, "profile-reviews/{$review->id}/") && ! Str::contains($path, '..')
            ? $path : null;
    }

    /** ['y' => 2019, 'm' => 4, 'd' => 29] => "2019-04-29" (month and day optional). */
    private static function when(mixed $parts): ?string
    {
        $parts = is_array($parts) ? $parts : [];
        $year = (int) ($parts['y'] ?? 0);
        if ($year < 1900) {
            return null;
        }
        $month = (int) ($parts['m'] ?? 0);
        $day = (int) ($parts['d'] ?? 0);

        return $year.($month ? sprintf('-%02d', $month).($day ? sprintf('-%02d', $day) : '') : '');
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @param  list<array<string, mixed>>  $additions
     * @param  list<array{year: string, text: string}>  $journey
     */
    private function assertComplete(array $items, array $additions, array $journey): void
    {
        $errors = [];
        foreach ($items as $item) {
            if ($item['decision'] !== 'edit') {
                continue;
            }
            if (in_array($item['type'], ['when', 'select', 'scope'], true) && blank($item['new'])) {
                $errors["v.{$item['key']}"] = __('site.review.error_missing_value', ['label' => $item['label']]);
            }
            if (($item['min_words'] ?? 0) && ProfileReviewItems::words($item['new']) < $item['min_words']) {
                $errors["v.{$item['key']}"] = __('site.review.error_words', ['label' => $item['title'] ?? $item['label'], 'n' => $item['min_words']]);
            }
        }
        foreach ($additions as $addition) {
            if (blank($addition['title']) || blank($addition['when'])) {
                $errors['add'] = __('site.review.error_addition');
            } elseif (ProfileReviewItems::words($addition['description']) < ProfileReviewItems::MIN_WORDS) {
                $errors['add_words'] = __('site.review.error_words', ['label' => $addition['title'], 'n' => ProfileReviewItems::MIN_WORDS]);
            }
        }
        foreach ($journey as $station) {
            if (ProfileReviewItems::words($station['text']) > ProfileReviewItems::STATION_WORDS) {
                $errors['j'] = __('site.review.error_station', ['n' => ProfileReviewItems::STATION_WORDS]);
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
