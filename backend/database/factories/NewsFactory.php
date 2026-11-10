<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<News> */
class NewsFactory extends Factory
{
    public function definition(): array
    {
        return ['slug' => fake()->unique()->slug(3), 'title' => ['ar' => 'خبر '.fake()->unique()->numberBetween(1, 99999)],
            'body' => ['ar' => '<p>نص الخبر.</p>'], 'happened_at' => now()->subMonth(), 'is_published' => true];
    }
}
