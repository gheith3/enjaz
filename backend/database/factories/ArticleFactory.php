<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(3),
            'title' => ['ar' => 'مقال '.fake()->unique()->numberBetween(1, 99999)],
            'dek' => ['ar' => 'سطر تعريفي بالمقال.'],
            'lead' => ['ar' => 'الفقرة الأولى من المقال.'],
            'body' => ['ar' => '<p>نص المقال.</p>'],
            'is_published' => true,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
