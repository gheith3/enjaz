<?php

declare(strict_types=1);

/**
 * Contact resource translations (Arabic).
 *
 * Usage: __('contact.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'رسالة تواصل',
    'model_label_plural' => 'رسائل التواصل',

    'status' => [
        'new' => 'جديد',
        'read' => 'مقروء',
        'in_progress' => 'قيد المعالجة',
        'completed' => 'مكتمل',
    ],

    'navigation' => [
        'badge_tooltip' => 'رسائل تواصل جديدة',
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'message' => 'الرسالة',
            'status' => 'الحالة',
            'ip_address' => 'عنوان IP',
            'created_at' => 'تاريخ الاستلام',
        ],
    ],

    'infolist' => [
        'sections' => [
            'sender_information' => 'معلومات المرسل',
            'message' => 'الرسالة',
            'timestamps' => 'التواريخ',
        ],
        'fields' => [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'ip_address' => 'عنوان IP',
            'status' => 'الحالة',
            'created_at' => 'تاريخ الإنشاء',
        ],
    ],

    'filters' => [
        'status' => 'الحالة',
        'from' => 'من',
        'until' => 'إلى',
        'from_indicator' => 'من :date',
        'until_indicator' => 'إلى :date',
    ],
];
