<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Field> */
class FieldFactory extends Factory
{
    public function definition(): array
    {
        return ['slug' => fake()->unique()->slug(1), 'name' => ['ar' => fake()->unique()->word()], 'icon' => 'cat-sports', 'is_active' => true];
    }
}
