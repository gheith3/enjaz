<?php

namespace Database\Factories;

use App\Models\Writer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Writer> */
class WriterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => ['ar' => 'كاتب '.fake()->unique()->numberBetween(1, 99999)],
            'role' => ['ar' => 'كاتب رأي'],
            'bio' => ['ar' => 'نبذة عن الكاتب.'],
            'is_published' => true,
        ];
    }
}
