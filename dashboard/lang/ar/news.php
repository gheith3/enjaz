<?php

declare(strict_types=1);

/**
 * News resource translations (Arabic).
 *
 * Usage: __('news.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'خبر',
    'model_label_plural' => 'الأخبار',

    'form' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'details' => 'الغلاف والتفاصيل',
            'details_description' => 'صورة الغلاف وتاريخ النشر والجهة المرتبطة',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'excerpt_ar' => 'المقتطف (العربية)',
            'excerpt_en' => 'المقتطف (الإنجليزية)',
            'body_ar' => 'نص الخبر (العربية)',
            'body_en' => 'نص الخبر (الإنجليزية)',
            'cover_image' => 'صورة الغلاف',
            'published_at' => 'تاريخ النشر',
            'organization' => 'الجهة',
            'show_on_about' => 'العرض في صفحة من نحن',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'title_ar' => 'أدخل عنوان الخبر بالعربية',
            'title_en' => 'أدخل عنوان الخبر بالإنجليزية',
            'excerpt_ar' => 'ملخص قصير بالعربية',
            'excerpt_en' => 'ملخص قصير بالإنجليزية',
            'body_ar' => 'النص الكامل للخبر بالعربية',
            'body_en' => 'النص الكامل للخبر بالإنجليزية',
        ],
        'helpers' => [
            'cover_image' => 'الأبعاد الموصى بها 345:208',
            'show_on_about' => 'عرض هذا الخبر في صفحة من نحن على الموقع العام',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'details' => 'الغلاف والتفاصيل',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'excerpt_ar' => 'المقتطف (العربية)',
            'excerpt_en' => 'المقتطف (الإنجليزية)',
            'body_ar' => 'نص الخبر (العربية)',
            'body_en' => 'نص الخبر (الإنجليزية)',
            'cover_image' => 'صورة الغلاف',
            'published_at' => 'تاريخ النشر',
            'organization' => 'الجهة',
            'show_on_about' => 'العرض في صفحة من نحن',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'الغلاف',
            'organization' => 'الجهة',
            'published_at' => 'تاريخ النشر',
            'is_published' => 'منشور',
            'show_on_about' => 'في من نحن',
        ],
    ],

    'filters' => [
        'organization' => 'الجهة',
        'is_published' => 'حالة النشر',
        'all_news' => 'جميع الأخبار',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
        'published_range' => 'تاريخ النشر',
        'published_from' => 'من تاريخ',
        'published_until' => 'إلى تاريخ',
    ],

];
