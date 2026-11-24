<?php

namespace App\Models;

use App\Support\ProfileReviews\ProfileReviewItems;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * مراجعة المنجز لصفحته. The team sends a private link (token); the achiever confirms («صحيح») or corrects
 * («تعديل») every point of their profile and may add achievements; the team then approves point by point
 * in the dashboard (ProfileReviewApplier) and the approved answers go live.
 *
 * items: list of points, see ProfileReviewItems. additions: achievements the achiever adds.
 */
class ProfileReview extends Model
{
    use HasFactory;

    public const STATUSES = [
        'sent' => 'أُنشئ الرابط',
        'opened' => 'فتحه المنجز',
        'submitted' => 'بانتظار الاعتماد',
        'returned' => 'أُعيد للمنجز',
        'approved' => 'اعتُمد ونُشر',
        'closed' => 'أُغلق',
    ];

    public const STATUS_COLORS = [
        'sent' => 'gray', 'opened' => 'info', 'submitted' => 'warning', 'returned' => 'danger', 'approved' => 'success', 'closed' => 'gray',
    ];

    /** Days a new link stays valid. */
    public const VALID_DAYS = 21;

    protected $fillable = [
        'achiever_id', 'token', 'status', 'items', 'additions', 'journey', 'socials', 'gallery', 'videos', 'contact', 'note', 'admin_note', 'expires_at', 'opened_at',
        'submitted_at', 'reviewed_at', 'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array', 'additions' => 'array', 'journey' => 'array', 'socials' => 'array', 'gallery' => 'array',
            'videos' => 'array', 'contact' => 'array', 'expires_at' => 'datetime', 'opened_at' => 'datetime',
            'submitted_at' => 'datetime', 'reviewed_at' => 'datetime',
        ];
    }

    /** A new link for an achiever, with a snapshot of every point of their profile. */
    public static function start(Achiever $achiever): self
    {
        return static::create([
            'achiever_id' => $achiever->id,
            'token' => Str::random(48),
            'status' => 'sent',
            'items' => ProfileReviewItems::build($achiever),
            'additions' => [],
            ...ProfileReviewItems::sections($achiever),
            'expires_at' => now()->addDays(self::VALID_DAYS),
        ]);
    }

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class)->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'submitted');
    }

    public function url(): string
    {
        return route('profile-review.show', $this->token);
    }

    /** The achiever can still open the link and change their answers. */
    public function isEditable(): bool
    {
        return in_array($this->status, ['sent', 'opened', 'returned'], true)
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * What the achiever changed: corrected points, and the list parts they changed.
     *
     * @return array{total: int, edited: int, additions: int, photos: int, lists: int}
     */
    public function progress(): array
    {
        $items = collect($this->items ?? []);
        $lists = collect(['journey', 'socials', 'videos', 'contact'])->filter(fn ($part) => $this->changed($part))->count();

        return [
            'total' => $items->count(),
            'edited' => $items->where('decision', 'edit')->count(),
            'additions' => count($this->additions ?? []),
            'photos' => count($this->gallery ?? []),
            'lists' => $lists,
        ];
    }

    /** Whether the achiever changed one of the list parts (journey, socials, videos, contact). */
    public function changed(string $part): bool
    {
        $value = $this->{$part} ?? [];

        return ($value['new'] ?? null) !== ($value['old'] ?? null);
    }

    /** Group of the dashboard «مراجعات المنجزين»: pending (not reviewed yet), reviewed (waiting for approval), published. */
    public function stage(): string
    {
        return match ($this->status) {
            'submitted' => 'reviewed',
            'approved', 'closed' => 'published',
            default => 'pending',
        };
    }

    /** The message sent with the link (WhatsApp, e-mail). */
    public function invitation(): string
    {
        $name = $this->achiever?->shortName() ?: '';

        return __('site.review.invite_text', ['name' => $name, 'url' => $this->url(), 'date' => $this->expires_at?->format('Y/m/d') ?? '']);
    }
}
