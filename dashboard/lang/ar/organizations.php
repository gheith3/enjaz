<?php

declare(strict_types=1);

/**
 * Organizations resource translations (Arabic).
 *
 * Usage: __('organizations.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'مؤسسة',
    'model_label_plural' => 'المؤسسات',

    'form' => [
        'sections' => [
            'identity' => 'الهوية',
            'about' => 'نبذة',
            'branding' => 'الهوية البصرية',
            'manager' => 'المدير',
            'links' => 'الروابط',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'organization_type' => 'نوع المؤسسة',
            'name_ar' => 'الاسم (بالعربية)',
            'name_en' => 'الاسم (بالإنجليزية)',
            'description_ar' => 'الوصف (بالعربية)',
            'description_en' => 'الوصف (بالإنجليزية)',
            'logo' => 'الشعار',
            'logo_white' => 'الشعار الأبيض',
            'manager_name_ar' => 'اسم المدير (بالعربية)',
            'manager_name_en' => 'اسم المدير (بالإنجليزية)',
            'manager_message_ar' => 'رسالة المدير (بالعربية)',
            'manager_message_en' => 'رسالة المدير (بالإنجليزية)',
            'manager_image' => 'صورة المدير',
            'website_url' => 'رابط الموقع الإلكتروني',
            'is_published' => 'منشورة',
        ],
        'placeholders' => [
            'name_ar' => 'اسم المؤسسة',
            'name_en' => 'Organization name',
            'website_url' => 'https://example.com',
        ],
        'helpers' => [
            'logo' => 'بحد أقصى 280×190، يُعرض على بطاقة بيضاء',
            'logo_white' => 'النسخة البيضاء من الشعار، تُستخدم على الترويسات الملوّنة',
        ],
    ],

    'table' => [
        'columns' => [
            'logo' => 'الشعار',
            'name' => 'الاسم',
            'website_url' => 'الموقع الإلكتروني',
            'is_published' => 'منشورة',
            'sort_order' => 'ترتيب العرض',
        ],
    ],

    'filters' => [
        'type' => 'نوع المؤسسة',
        'is_published' => 'حالة النشر',
        'all' => 'الكل',
        'published_only' => 'المنشورة فقط',
        'unpublished_only' => 'غير المنشورة فقط',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'الهوية',
            'about' => 'نبذة',
            'branding' => 'الهوية البصرية',
            'manager' => 'المدير',
            'links' => 'الروابط',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'organization_type' => 'نوع المؤسسة',
            'name_ar' => 'الاسم (بالعربية)',
            'name_en' => 'الاسم (بالإنجليزية)',
            'description_ar' => 'الوصف (بالعربية)',
            'description_en' => 'الوصف (بالإنجليزية)',
            'logo' => 'الشعار',
            'logo_white' => 'الشعار الأبيض',
            'manager_name_ar' => 'اسم المدير (بالعربية)',
            'manager_name_en' => 'اسم المدير (بالإنجليزية)',
            'manager_message_ar' => 'رسالة المدير (بالعربية)',
            'manager_message_en' => 'رسالة المدير (بالإنجليزية)',
            'manager_image' => 'صورة المدير',
            'website_url' => 'رابط الموقع الإلكتروني',
            'is_published' => 'منشورة',
        ],
    ],
];
