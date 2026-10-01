<?php

declare(strict_types=1);

/**
 * Team Members resource translations (Arabic).
 *
 * Usage: __('team_members.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'عضو فريق',
    'model_label_plural' => 'فريق العمل',

    'form' => [
        'sections' => [
            'member' => 'العضو',
            'member_description' => 'اسم عضو الفريق ومنصبه',
            'photo' => 'الصورة',
            'photo_description' => 'صورة شخصية لعضو الفريق',
            'publishing' => 'النشر',
            'publishing_description' => 'الرابط المختصر والظهور والترتيب',
        ],
        'fields' => [
            'position_ar' => 'المنصب (بالعربية)',
            'position_en' => 'المنصب (بالإنجليزية)',
            'photo' => 'الصورة',
            'is_published' => 'منشور',
        ],
        'placeholders' => [
            'name_ar' => 'مثال: محمد البلوشي',
            'name_en' => 'e.g. Mohammed Al Balushi',
            'position_ar' => 'مثال: مدير المشروع',
            'position_en' => 'e.g. Project Manager',
        ],
        'helpers' => [
            'photo' => 'صورة شخصية — أبعاد 333:359',
        ],
    ],

    'table' => [
        'columns' => [
            'position_en' => 'المنصب (بالإنجليزية)',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [
        'is_published' => 'منشور',
    ],

    'infolist' => [
        'sections' => [
            'member' => 'العضو',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'position_ar' => 'المنصب (بالعربية)',
            'position_en' => 'المنصب (بالإنجليزية)',
            'is_published' => 'منشور',
        ],
    ],
];
