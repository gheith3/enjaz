<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MenuItem> */
class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location' => 'header',
            'label' => ['ar' => fake()->unique()->words(2, true)],
            'url' => '/'.fake()->unique()->slug(2),
            'show_in_desktop_nav' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }
}
