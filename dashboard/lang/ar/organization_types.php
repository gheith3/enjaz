<?php

declare(strict_types=1);

/**
 * Organization types resource translations (Arabic).
 *
 * Usage: __('organization_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'نوع المؤسسة',
    'model_label_plural' => 'أنواع المؤسسات',

    'form' => [
        'sections' => [
            'identity' => 'الهوية',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'title_ar' => 'العنوان (بالعربية)',
            'title_en' => 'العنوان (بالإنجليزية)',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'title_ar' => 'نوع المؤسسة',
            'title_en' => 'Organization type',
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'العنوان',
            'organizations_count' => 'عدد المؤسسات',
            'is_published' => 'منشور',
            'sort_order' => 'ترتيب العرض',
        ],
    ],

    'filters' => [
        'is_published' => 'حالة النشر',
        'all' => 'الكل',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'الهوية',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'title_ar' => 'العنوان (بالعربية)',
            'title_en' => 'العنوان (بالإنجليزية)',
            'organizations_count' => 'عدد المؤسسات',
            'is_published' => 'منشور',
        ],
    ],
];
