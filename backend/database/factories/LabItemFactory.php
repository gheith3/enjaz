<?php

namespace Database\Factories;

use App\Models\LabItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LabItem> */
class LabItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kind' => 'invention',
            'slug' => fake()->unique()->slug(2),
            'title' => ['ar' => 'ابتكار '.fake()->unique()->numberBetween(1, 99999)],
            'who' => ['ar' => 'مبتكر عُماني'],
            'lead' => ['ar' => 'فقرة تعريفية بالابتكار.'],
            'body' => ['ar' => '<p>نص القصة.</p>'],
            'medal' => 'gold',
            'event' => 'ITEX',
            'event_label' => ['ar' => 'ITEX 2026'],
            'happened_at' => '2026-05-01',
            'is_published' => true,
        ];
    }

    public function publication(): static
    {
        return $this->state([
            'kind' => 'publication', 'medal' => null, 'event' => null, 'event_label' => null,
            'field' => ['ar' => 'العلوم'], 'authors' => ['ar' => 'باحث عُماني، باحث آخر'], 'journal' => ['ar' => 'مجلة علمية'],
            'finding_value' => ['ar' => '٩٥٪'], 'finding_text' => ['ar' => 'نتيجة بارزة.'], 'summary' => ['ar' => 'ملخص البحث.'],
        ]);
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
