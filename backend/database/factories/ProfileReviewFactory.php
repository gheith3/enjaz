<?php

namespace Database\Factories;

use App\Models\Achiever;
use App\Models\ProfileReview;
use App\Support\ProfileReviews\ProfileReviewItems;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProfileReview> */
class ProfileReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'achiever_id' => Achiever::factory(),
            'token' => Str::random(48),
            'status' => 'sent',
            'items' => [],
            'additions' => [],
            'expires_at' => now()->addDays(ProfileReview::VALID_DAYS),
        ];
    }

    /** With the snapshot of the achiever's profile, as ProfileReview::start() builds it. */
    public function snapshot(): static
    {
        return $this->afterCreating(function (ProfileReview $review) {
            $review->update(['items' => ProfileReviewItems::build($review->achiever)]);
        });
    }
}
