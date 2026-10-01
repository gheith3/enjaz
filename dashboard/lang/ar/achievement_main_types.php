<?php

declare(strict_types=1);

/**
 * Achievement main types resource translations (Arabic).
 *
 * Usage: __('achievement_main_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'مجال رئيسي',
    'model_label_plural' => 'المجالات الرئيسية',

    'form' => [
        'sections' => [
            'titles' => 'العناوين',
            'appearance' => 'المظهر',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'icon' => 'الأيقونة',
            'show_on_home' => 'العرض في الرئيسية',
            'is_published' => 'منشور',
        ],
        'placeholders' => [],
        'helpers' => [
            'slug' => 'يُولَّد تلقائيًا من العنوان العربي. عدّله إذا كانت القيمة المولدة فارغة.',
            'show_on_home' => 'عرض هذا المجال الرئيسي في الصفحة الرئيسية العامة.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'titles' => 'العناوين',
            'appearance' => 'المظهر',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'icon' => 'الأيقونة',
            'show_on_home' => 'العرض في الرئيسية',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'icon' => 'الأيقونة',
            'sub_types_count' => 'الأنواع الفرعية',
            'show_on_home' => 'العرض في الرئيسية',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'is_published' => 'منشور',
        'show_on_home' => 'العرض في الرئيسية',
        'all' => 'الكل',
        'published_only' => 'المنشور فقط',
        'unpublished_only' => 'غير المنشور فقط',
        'shown_on_home' => 'معروض في الرئيسية',
        'hidden_from_home' => 'مخفي من الرئيسية',
    ],
];
