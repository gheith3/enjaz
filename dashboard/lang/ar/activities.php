<?php

declare(strict_types=1);

/**
 * Activities resource translations (Arabic).
 *
 * Usage: __('activities.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'سجل النشاط',
    'model_label_plural' => 'سجلات النشاط',

    'navigation' => [
        'badge_tooltip' => 'أنشطة اليوم',
    ],

    'tabs' => [
        'all' => 'الكل',
        'today' => 'اليوم',
        'created' => 'تم إنشاؤه',
        'updated' => 'تم تحديثه',
        'deleted' => 'تم حذفه',
    ],

    'events' => [
        'created' => 'إنشاء',
        'updated' => 'تحديث',
        'deleted' => 'حذف',
        'restored' => 'استعادة',
        'login' => 'تسجيل دخول',
        'logout' => 'تسجيل خروج',
    ],

    'actions' => [
        'view_subject' => 'عرض العنصر',
        'view_batch' => 'عرض الدفعة',
    ],

    'infolist' => [
        'sections' => [
            'causer_information' => 'معلومات المتسبب',
            'subject_history_timeline' => 'السجل الزمني للعنصر',
            'activity_details' => 'تفاصيل النشاط',
            'additional_properties' => 'خصائص إضافية',
            'batch_information' => 'معلومات الدفعة',
            'timestamps' => 'الطوابع الزمنية',
        ],
        'fields' => [
            'causer_type' => 'نوع المتسبب',
            'causer_id' => 'معرّف المتسبب',
            'causer_name' => 'اسم المتسبب',
            'causer_email' => 'بريد المتسبب',
            'activity_id' => 'معرّف النشاط',
            'log_name' => 'اسم السجل',
            'event' => 'الحدث',
            'subject_type' => 'نوع العنصر',
            'subject_id' => 'معرّف العنصر',
            'subject_name' => 'اسم العنصر',
            'batch_uuid' => 'معرّف الدفعة',
            'activities_in_batch' => 'الأنشطة في الدفعة',
        ],
        'placeholders' => [
            'system' => 'النظام',
            'no_additional_properties' => 'لا توجد خصائص إضافية',
        ],
    ],

    'table' => [
        'columns' => [
            'event' => 'الحدث',
            'subject' => 'العنصر',
            'caused_by' => 'بواسطة',
            'batch' => 'الدفعة',
        ],
    ],

    'filters' => [
        'log_name' => 'اسم السجل',
        'event' => 'الحدث',
        'subject_type' => 'نوع العنصر',
        'caused_by' => 'بواسطة',
        'from' => 'من',
        'until' => 'إلى',
        'from_indicator' => 'من :date',
        'until_indicator' => 'إلى :date',
        'has_batch' => 'ضمن دفعة',
    ],

];
