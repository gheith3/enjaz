<?php

declare(strict_types=1);

/**
 * Publication media resource translations (Arabic).
 *
 * Usage: __('publication_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'إصدار',
    'model_label_plural' => 'الإصدارات',

    'form' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'publication' => 'الإصدار',
            'publication_description' => 'ملف الإصدار وصورة الغلاف',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'file' => 'ملف الإصدار',
            'cover_image' => 'صورة الغلاف',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'title_ar' => 'أدخل عنوان الإصدار بالعربية',
            'title_en' => 'أدخل عنوان الإصدار بالإنجليزية',
            'description_ar' => 'وصف الإصدار بالعربية',
            'description_en' => 'وصف الإصدار بالإنجليزية',
        ],
        'helpers' => [
            'file' => 'ملفات PDF فقط',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'publication' => 'الإصدار',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'file' => 'ملف الإصدار',
            'cover_image' => 'صورة الغلاف',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'الغلاف',
            'file' => 'الملف',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'is_active' => 'حالة النشاط',
        'is_published' => 'حالة النشر',
        'all_publications' => 'جميع الإصدارات',
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
    ],

];
