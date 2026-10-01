<?php

declare(strict_types=1);

/**
 * Testimonials resource translations (Arabic).
 *
 * Usage: __('testimonials.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'شهادة',
    'model_label_plural' => 'قالوا عنا',

    'form' => [
        'sections' => [
            'person' => 'الشخص',
            'person_description' => 'اسم الشخص صاحب الشهادة ومنصبه',
            'quote' => 'الشهادة',
            'quote_description' => 'نص الشهادة',
            'photo' => 'الصورة',
            'photo_description' => 'صورة مربعة على نمط الصورة الرمزية',
            'publishing' => 'النشر',
            'publishing_description' => 'الرابط المختصر والظهور والترتيب',
        ],
        'fields' => [
            'person_name_ar' => 'اسم الشخص (بالعربية)',
            'person_name_en' => 'اسم الشخص (بالإنجليزية)',
            'position_ar' => 'المنصب (بالعربية)',
            'position_en' => 'المنصب (بالإنجليزية)',
            'quote_ar' => 'الشهادة (بالعربية)',
            'quote_en' => 'الشهادة (بالإنجليزية)',
            'photo' => 'الصورة',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'person_name_ar' => 'مثال: د. سالم الحارثي',
            'person_name_en' => 'e.g. Dr. Salim Al Harthi',
            'position_ar' => 'مثال: رئيس مجلس الإدارة',
            'position_en' => 'e.g. Chairman of the Board',
        ],
        'helpers' => [
            'photo' => 'صورة مربعة — أبعاد 252:252',
        ],
    ],

    'table' => [
        'columns' => [
            'person_name' => 'الشخص',
            'quote' => 'الشهادة',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'is_published' => 'منشور',
    ],

    'infolist' => [
        'sections' => [
            'person' => 'الشخص',
            'quote' => 'الشهادة',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'person_name_ar' => 'اسم الشخص (بالعربية)',
            'person_name_en' => 'اسم الشخص (بالإنجليزية)',
            'position_ar' => 'المنصب (بالعربية)',
            'position_en' => 'المنصب (بالإنجليزية)',
            'quote_ar' => 'الشهادة (بالعربية)',
            'quote_en' => 'الشهادة (بالإنجليزية)',
            'is_published' => 'منشور',
        ],
    ],
];
