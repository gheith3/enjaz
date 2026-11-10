<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Organization> */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'organization_type_id' => OrganizationType::factory(),
            'name' => ['ar' => 'مؤسسة '.fake()->unique()->numberBetween(1, 99999)],
            'is_published' => true,
        ];
    }
}
