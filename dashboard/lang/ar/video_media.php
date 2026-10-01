<?php

declare(strict_types=1);

/**
 * Video media resource translations (Arabic).
 *
 * Usage: __('video_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'فيديو',
    'model_label_plural' => 'مقاطع الفيديو',

    'form' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'video' => 'الفيديو',
            'video_description' => 'رابط الفيديو وصورة الغلاف والشخصية المرتبطة',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'url' => 'رابط الفيديو',
            'cover_image' => 'صورة الغلاف',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'title_ar' => 'أدخل عنوان الفيديو بالعربية',
            'title_en' => 'أدخل عنوان الفيديو بالإنجليزية',
            'description_ar' => 'وصف الفيديو بالعربية',
            'description_en' => 'وصف الفيديو بالإنجليزية',
            'url' => 'https://youtube.com/watch?v=...',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'المحتوى بالعربية',
            'content_en' => 'المحتوى بالإنجليزية',
            'video' => 'الفيديو',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'url' => 'رابط الفيديو',
            'cover_image' => 'صورة الغلاف',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'الغلاف',
            'url' => 'الرابط',
            'public_figure' => 'الشخصية العامة',
            'is_active' => 'نشط',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'public_figure' => 'الشخصية العامة',
        'is_active' => 'حالة النشاط',
        'is_published' => 'حالة النشر',
        'all_videos' => 'جميع المقاطع',
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
    ],

];
