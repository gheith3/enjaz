<?php

namespace Database\Factories;

use App\Models\Scope;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Scope> */
class ScopeFactory extends Factory
{
    public function definition(): array
    {
        return ['slug' => 'international', 'name' => ['ar' => 'دولي'], 'achievement_label' => ['ar' => 'إنجاز دولي'], 'is_international' => true];
    }

    public function local(): static
    {
        return $this->state(['slug' => 'local', 'name' => ['ar' => 'محلي'], 'achievement_label' => ['ar' => 'إنجاز محلي'], 'is_international' => false]);
    }
}
