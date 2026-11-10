<?php

namespace Database\Factories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Award> */
class AwardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'kind' => 'culture',
            'name' => ['ar' => 'جائزة '.fake()->unique()->numberBetween(1, 99999)],
            'is_published' => true,
        ];
    }

    public function kind(string $kind): static
    {
        return $this->state(['kind' => $kind]);
    }
}
