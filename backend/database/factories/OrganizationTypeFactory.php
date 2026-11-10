<?php

namespace Database\Factories;

use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrganizationType> */
class OrganizationTypeFactory extends Factory
{
    public function definition(): array
    {
        return ['slug' => fake()->unique()->slug(1), 'name' => ['ar' => 'أكاديمي'], 'label' => ['ar' => 'مؤسسة أكاديمية'],
            'group_label' => ['ar' => 'المؤسسات الأكاديمية'], 'template' => 'university', 'css_class' => 't-edu'];
    }

    public function template(string $template): static
    {
        return $this->state(['template' => $template]);
    }
}
