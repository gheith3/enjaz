<?php

declare(strict_types=1);

/**
 * Sub achievement types resource translations (Arabic).
 *
 * Usage: __('sub_achievement_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'نوع فرعي',
    'model_label_plural' => 'الأنواع الفرعية',

    'form' => [
        'sections' => [
            'classification' => 'التصنيف',
            'titles' => 'العناوين',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'main_type' => 'المجال الرئيسي',
            'is_published' => 'منشور',
        ],
        'placeholders' => [],
        'helpers' => [
            'slug' => 'يُولَّد تلقائيًا من العنوان العربي. عدّله إذا كانت القيمة المولدة فارغة.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'titles' => 'العناوين',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'main_type' => 'المجال الرئيسي',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'main_type' => 'المجال الرئيسي',
            'achievements_count' => 'الإنجازات',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'main_type' => 'المجال الرئيسي',
        'is_published' => 'منشور',
        'all' => 'الكل',
        'published_only' => 'المنشور فقط',
        'unpublished_only' => 'غير المنشور فقط',
    ],
];
