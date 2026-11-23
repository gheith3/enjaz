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
 * The achiever's private review page (/review/{token}): plain, Arabic, never the profile's design (nothing of the
 * launch is revealed). Answers are saved on the review; nothing reaches the site before the team approves it.
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
            'add' => ['array', 'max:40'],
            'add.*.title' => ['nullable', 'string', 'max:255'],
            'add.*.description' => ['nullable', 'string', 'max:20000'],
            'add.*.discipline' => ['nullable', 'string', 'max:255'],
            'add.*.issuer' => ['nullable', 'string', 'max:255'],
            'add.*.country' => ['nullable', 'string', 'max:255'],
            'add.*.scope_id' => ['nullable', 'integer'],
            'add.*.cover' => ['nullable', 'string', 'max:255'],
            'add.*.y' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'add.*.m' => ['nullable', 'integer', 'min:1', 'max:12'],
            'add.*.d' => ['nullable', 'integer', 'min:1', 'max:31'],
            'note' => ['nullable', 'string', 'max:10000'],
        ]);

        $decisions = $request->input('d', []);
        $items = collect($review->items)->map(function (array $item) use ($request, $decisions, $review) {
            $key = $item['key'];
            $item['decision'] = $decisions[$key] ?? $item['decision'] ?? null;
            $item['new'] = match ($item['type']) {
                'when' => self::when($request->input("w.$key", [])),
                'image' => $this->ownUpload($review, $request->input("v.$key")),
                default => $request->input("v.$key"),
            };
            if ($item['decision'] !== 'edit') {
                $item['new'] = null;
            }
            $item['approved'] = null;

            return $item;
        })->all();

        $additions = collect($request->input('add', []))->map(fn (array $add, $uid) => [
            'uid' => (string) Str::of((string) $uid)->replaceMatches('/[^A-Za-z0-9_]/', '')->limit(20, ''),
            'title' => trim((string) ($add['title'] ?? '')),
            'when' => self::when($add),
            'scope_id' => $add['scope_id'] ?? null,
            'discipline' => $add['discipline'] ?? null,
            'issuer' => $add['issuer'] ?? null,
            'country' => $add['country'] ?? null,
            'description' => $add['description'] ?? null,
            'cover' => $this->ownUpload($review, $add['cover'] ?? null),
            'approved' => null,
        ])->filter(fn ($add) => filled($add['title']) || filled($add['description']))->values()->all();

        // Answers are kept even when the submit is refused, so nothing the achiever wrote is lost.
        $review->update(['items' => $items, 'additions' => $additions, 'note' => $request->input('note')]);

        if ($request->input('action') === 'submit') {
            $this->assertComplete($items, $additions);
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

        return response()->json(['path' => $path]);
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
     */
    private function assertComplete(array $items, array $additions): void
    {
        $errors = [];
        foreach ($items as $item) {
            if (! in_array($item['decision'], ['ok', 'edit'], true)) {
                $errors["d.{$item['key']}"] = __('site.review.error_undecided', ['label' => $item['label']]);
            } elseif ($item['decision'] === 'edit' && in_array($item['type'], ['image', 'when', 'select'], true) && blank($item['new'])) {
                $errors["v.{$item['key']}"] = __('site.review.error_missing_value', ['label' => $item['label']]);
            }
        }
        foreach ($additions as $addition) {
            if (blank($addition['title']) || blank($addition['when'])) {
                $errors['add'] = __('site.review.error_addition');
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
