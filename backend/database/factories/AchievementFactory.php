<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Achievement> */
class AchievementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(3),
            'title' => ['ar' => 'إنجاز '.fake()->unique()->numberBetween(1, 99999)],
            'excerpt' => ['ar' => 'ملخص الإنجاز'],
            'body' => ['ar' => '<p>الفقرة الأولى.</p><p>الفقرة الثانية.</p><p>الفقرة الثالثة.</p>'],
            'achieved_at' => fake()->dateTimeBetween('-10 years')->format('Y-m-d'),
            'date_precision' => 'day',
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
