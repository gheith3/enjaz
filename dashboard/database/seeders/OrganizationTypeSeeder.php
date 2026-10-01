<?php

namespace Database\Seeders;

use App\Models\OrganizationType;
use Illuminate\Database\Seeder;

class OrganizationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['title_ar' => 'حكومي', 'title_en' => 'Government', 'slug' => 'government'],
            ['title_ar' => 'خاص', 'title_en' => 'Private', 'slug' => 'private'],
        ];

        foreach ($types as $index => $type) {
            OrganizationType::updateOrCreate(
                ['slug' => $type['slug']],
                [
                    'title_ar' => $type['title_ar'],
                    'title_en' => $type['title_en'],
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
