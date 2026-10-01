<?php

namespace Database\Seeders;

use App\Models\AchievementMainType;
use Illuminate\Database\Seeder;

class AchievementMainTypeSeeder extends Seeder
{
    public function run(): void
    {
        $mainTypes = [
            ['title_ar' => 'الرياضة', 'title_en' => 'Sports', 'slug' => 'sports'],
            ['title_ar' => 'الثقافة', 'title_en' => 'Culture', 'slug' => 'culture'],
            ['title_ar' => 'العلوم', 'title_en' => 'Science', 'slug' => 'science'],
            ['title_ar' => 'العسكرية', 'title_en' => 'Military', 'slug' => 'military'],
            ['title_ar' => 'الطبي', 'title_en' => 'Medical', 'slug' => 'medical'],
            ['title_ar' => 'الإبتكارات', 'title_en' => 'Innovations', 'slug' => 'innovations'],
            ['title_ar' => 'الأدب', 'title_en' => 'Literature', 'slug' => 'literature'],
            ['title_ar' => 'طلبة المدارس', 'title_en' => 'School Students', 'slug' => 'students'],
            ['title_ar' => 'الفنون', 'title_en' => 'Arts', 'slug' => 'arts'],
            ['title_ar' => 'ريادة الأعمال', 'title_en' => 'Entrepreneurship', 'slug' => 'entrepreneurship'],
        ];

        foreach ($mainTypes as $index => $mainType) {
            AchievementMainType::updateOrCreate(
                ['slug' => $mainType['slug']],
                [
                    'title_ar' => $mainType['title_ar'],
                    'title_en' => $mainType['title_en'],
                    'show_on_home' => $index < 3,
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
