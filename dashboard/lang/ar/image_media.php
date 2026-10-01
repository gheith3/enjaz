<?php

declare(strict_types=1);

/**
 * Image media (photo albums) resource translations (Arabic).
 *
 * Usage: __('image_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'ألبوم صور',
    'model_label_plural' => 'ألبومات الصور',

    'form' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'album' => 'الألبوم',
            'album_description' => 'صور الألبوم والشخصية المرتبطة',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'images' => 'الصور',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'title_ar' => 'أدخل عنوان الألبوم بالعربية',
            'title_en' => 'أدخل عنوان الألبوم بالإنجليزية',
            'description_ar' => 'وصف الألبوم بالعربية',
            'description_en' => 'وصف الألبوم بالإنجليزية',
        ],
        'helpers' => [
            'images' => 'ارفع عدة صور؛ اسحب لإعادة ترتيبها',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'album' => 'الألبوم',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'images' => 'الصور',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'images_count' => 'الصور',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'public_figure' => 'الشخصية العامة',
        'is_active' => 'حالة النشاط',
        'is_published' => 'حالة النشر',
        'all_albums' => 'جميع الألبومات',
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
    ],

];
