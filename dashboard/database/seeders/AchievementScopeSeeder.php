<?php

namespace Database\Seeders;

use App\Models\AchievementScope;
use Illuminate\Database\Seeder;

class AchievementScopeSeeder extends Seeder
{
    public function run(): void
    {
        $scopes = [
            ['title_ar' => 'دولي', 'title_en' => 'International', 'slug' => 'international'],
            ['title_ar' => 'محلي', 'title_en' => 'Local', 'slug' => 'local'],
        ];

        foreach ($scopes as $index => $scope) {
            AchievementScope::updateOrCreate(
                ['slug' => $scope['slug']],
                [
                    'title_ar' => $scope['title_ar'],
                    'title_en' => $scope['title_en'],
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
