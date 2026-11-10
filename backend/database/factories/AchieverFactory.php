<?php

namespace Database\Factories;

use App\Models\Achiever;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Achiever> */
class AchieverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => ['ar' => 'منجز '.fake()->unique()->numberBetween(1, 99999)],
            'gender' => fake()->randomElement(['m', 'f']),
            'headline' => ['ar' => 'وصف مختصر'],
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
