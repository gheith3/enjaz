<?php

declare(strict_types=1);

/**
 * Public Figures resource translations (Arabic).
 *
 * Usage: __('public_figures.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'منجز',
    'model_label_plural' => 'المنجزون',

    'form' => [
        'sections' => [
            'identity' => 'الهوية',
            'identity_description' => 'الاسم والوصف المهني المختصر بسطر واحد',
            'biography' => 'السيرة',
            'biography_description' => 'وصف مختصر وقصة الإنجاز الكاملة',
            'images' => 'الصور',
            'images_description' => 'الصورة الشخصية وصورة البطاقة والصورة المفرّغة وصورة الغلاف',
            'classification' => 'التصنيف',
            'classification_description' => 'نوع الإنجاز الرئيسي الذي ينتمي إليه المنجز',
            'flags' => 'التمييز',
            'flags_description' => 'إعدادات التمييز والظهور في سلايدر الصفحة الرئيسية',
            'social_contact' => 'التواصل الاجتماعي وبيانات الاتصال',
            'social_contact_description' => 'حسابات التواصل الاجتماعي وبيانات الاتصال',
            'publishing' => 'النشر',
            'publishing_description' => 'الرابط المختصر والظهور والترتيب',
        ],
        'fields' => [
            'headline_ar' => 'الوصف المختصر (بالعربية)',
            'headline_en' => 'الوصف المختصر (بالإنجليزية)',
            'description_ar' => 'الوصف (بالعربية)',
            'description_en' => 'الوصف (بالإنجليزية)',
            'story_ar' => 'القصة (بالعربية)',
            'story_en' => 'القصة (بالإنجليزية)',
            'image' => 'الصورة الشخصية',
            'tile_image' => 'صورة البطاقة',
            'cutout_image' => 'الصورة المفرّغة',
            'banner_image' => 'صورة الغلاف',
            'main_type' => 'نوع الإنجاز الرئيسي',
            'is_featured' => 'مميز',
            'in_home_carousel' => 'في سلايدر الرئيسية',
            'linkedin_url' => 'رابط لينكدإن',
            'twitter_url' => 'رابط إكس (تويتر)',
            'instagram_url' => 'رابط إنستغرام',
            'facebook_url' => 'رابط فيسبوك',
            'whatsapp_number' => 'رقم واتساب',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'name_ar' => 'مثال: أحمد بن سالم',
            'name_en' => 'e.g. Ahmed Al Salmi',
            'headline_ar' => 'مثال: أول عماني يصل قمة إفرست',
            'headline_en' => 'e.g. First Omani to summit Everest',
            'linkedin_url' => 'https://linkedin.com/in/username',
            'twitter_url' => 'https://x.com/username',
            'instagram_url' => 'https://instagram.com/username',
            'facebook_url' => 'https://facebook.com/username',
            'whatsapp_number' => '9XXX XXXX',
            'email' => 'name@example.com',
            'phone' => '9XXX XXXX',
        ],
        'helpers' => [
            'tile_image' => 'تُستخدم في بطاقات القوائم — أبعاد 351:427',
            'cutout_image' => 'صورة PNG بخلفية شفافة — أبعاد 848:477',
            'is_featured' => 'إبراز هذا المنجز في الأقسام المميزة',
            'in_home_carousel' => 'عرض هذا المنجز في سلايدر الصفحة الرئيسية',
        ],
    ],

    'table' => [
        'columns' => [
            'main_type' => 'النوع الرئيسي',
            'achievements_count' => 'الإنجازات',
            'is_featured' => 'مميز',
            'in_home_carousel' => 'السلايدر',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'main_type' => 'نوع الإنجاز الرئيسي',
        'is_featured' => 'مميز',
        'is_published' => 'منشور',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'الهوية',
            'biography' => 'السيرة',
            'images' => 'الصور',
            'flags' => 'التمييز',
            'social_contact' => 'التواصل الاجتماعي وبيانات الاتصال',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'headline_ar' => 'الوصف المختصر (بالعربية)',
            'headline_en' => 'الوصف المختصر (بالإنجليزية)',
            'description_ar' => 'الوصف (بالعربية)',
            'description_en' => 'الوصف (بالإنجليزية)',
            'story_ar' => 'القصة (بالعربية)',
            'story_en' => 'القصة (بالإنجليزية)',
            'image' => 'الصورة الشخصية',
            'tile_image' => 'صورة البطاقة',
            'cutout_image' => 'الصورة المفرّغة',
            'banner_image' => 'صورة الغلاف',
            'main_type' => 'نوع الإنجاز الرئيسي',
            'is_featured' => 'مميز',
            'in_home_carousel' => 'في سلايدر الرئيسية',
            'is_published' => 'منشور',
            'linkedin_url' => 'رابط لينكدإن',
            'twitter_url' => 'رابط إكس (تويتر)',
            'instagram_url' => 'رابط إنستغرام',
            'facebook_url' => 'رابط فيسبوك',
            'whatsapp_number' => 'رقم واتساب',
        ],
    ],
];
