<?php

declare(strict_types=1);

/**
 * Achievements resource translations (Arabic).
 *
 * Usage: __('achievements.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'إنجاز',
    'model_label_plural' => 'الإنجازات',

    'form' => [
        'sections' => [
            'classification' => 'التصنيف',
            'content_ar' => 'المحتوى (بالعربية)',
            'content_en' => 'المحتوى (بالإنجليزية)',
            'media' => 'الوسائط',
            'details' => 'التفاصيل',
            'relations' => 'الارتباطات',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'scope' => 'النطاق',
            'main_type' => 'المجال الرئيسي',
            'sub_type' => 'النوع الفرعي',
            'excerpt_ar' => 'المقتطف (بالعربية)',
            'excerpt_en' => 'المقتطف (بالإنجليزية)',
            'cover_image' => 'صورة الغلاف',
            'video_url' => 'رابط الفيديو',
            'achieved_at' => 'تاريخ الإنجاز',
            'country_ar' => 'الدولة (بالعربية)',
            'country_en' => 'الدولة (بالإنجليزية)',
            'public_figures' => 'المنجزون',
            'organizations' => 'المؤسسات',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'video_url' => 'https://youtube.com/watch?v=...',
        ],
        'helpers' => [
            'slug' => 'يُولَّد تلقائيًا من العنوان العربي. عدّله إذا كانت القيمة المولدة فارغة.',
            'sub_type' => 'تعتمد الخيارات على المجال الرئيسي المحدد.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'classification' => 'التصنيف',
            'content' => 'المحتوى',
            'media' => 'الوسائط',
            'details' => 'التفاصيل',
            'relations' => 'الارتباطات',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'scope' => 'النطاق',
            'main_type' => 'المجال الرئيسي',
            'sub_type' => 'النوع الفرعي',
            'excerpt_ar' => 'المقتطف (بالعربية)',
            'excerpt_en' => 'المقتطف (بالإنجليزية)',
            'cover_image' => 'صورة الغلاف',
            'video_url' => 'رابط الفيديو',
            'achieved_at' => 'تاريخ الإنجاز',
            'country_ar' => 'الدولة (بالعربية)',
            'country_en' => 'الدولة (بالإنجليزية)',
            'public_figures' => 'المنجزون',
            'organizations' => 'المؤسسات',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'الغلاف',
            'scope' => 'النطاق',
            'achieved_at' => 'تاريخ الإنجاز',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'scope' => 'النطاق',
        'main_type' => 'المجال الرئيسي',
        'is_published' => 'منشور',
        'all' => 'الكل',
        'published_only' => 'المنشور فقط',
        'unpublished_only' => 'غير المنشور فقط',
    ],
];
