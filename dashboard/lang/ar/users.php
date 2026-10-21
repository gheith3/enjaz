<?php

declare(strict_types=1);

/**
 * Users resource translations (Arabic).
 *
 * Usage: __('users.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'مستخدم',
    'model_label_plural' => 'المستخدمون',

    'form' => [
        'sections' => [
            'personal_information' => 'المعلومات الشخصية',
            'personal_information_description' => 'المعلومات الأساسية للمستخدم وبيانات التواصل',
            'security' => 'الأمان',
            'security_description' => 'إعدادات كلمة المرور والمصادقة',
            'user_type' => 'نوع المستخدم',
            'user_type_description' => 'تحديد دور المستخدم ونوعه',
            'status' => 'الحالة',
            'status_description' => 'إعدادات حالة الحساب',
        ],
        'fields' => [
            'email_address' => 'البريد الإلكتروني',
            'profile_photo' => 'الصورة الشخصية',
            'password' => 'كلمة المرور',
            'confirm_password' => 'تأكيد كلمة المرور',
            'email_verified_at' => 'تاريخ تأكيد البريد الإلكتروني',
            'roles' => 'الأدوار',
            'status' => 'الحالة',
        ],
        'placeholders' => [
            'name' => 'محمد أحمد',
            'email' => 'john@example.com',
            'phone' => '9XXX XXXX',
            'country_code' => '968',
        ],
        'helpers' => [
            'status' => 'المستخدمون الموقوفون والمدعوون لا يمكنهم الوصول إلى لوحة التحكم',
        ],
    ],

    'status' => [
        'active' => 'نشط',
        'suspended' => 'موقوف',
    ],

    'infolist' => [
        'sections' => [
            'personal_information' => 'المعلومات الشخصية',
            'role_access' => 'الدور والصلاحيات',
            'activity' => 'النشاط',
            'profile' => 'الملف الشخصي',
            'status' => 'الحالة',
        ],
        'fields' => [
            'email_address' => 'البريد الإلكتروني',
            'user_id' => 'معرّف المستخدم',
            'user_type' => 'نوع المستخدم',
            'account_created' => 'تاريخ إنشاء الحساب',
            'last_updated' => 'آخر تحديث',
            'email_verified' => 'تأكيد البريد الإلكتروني',
            'account_status' => 'حالة الحساب',
            'status' => 'الحالة',
        ],
        'placeholders' => [
            'not_verified' => 'غير مؤكد',
        ],
        'messages' => [
            'email_copied' => 'تم نسخ البريد الإلكتروني!',
        ],
    ],

    'table' => [
        'columns' => [
            'verified' => 'مؤكد',
            'joined' => 'تاريخ الانضمام',
            'two_factor' => 'المصادقة الثنائية',
        ],
        'placeholders' => [
            'not_verified' => 'غير مؤكد',
        ],
    ],

    'filters' => [
        'active_status' => 'حالة النشاط',
        'status' => 'الحالة',
        'all_users' => 'جميع المستخدمين',
        'active_only' => 'النشطون فقط',
        'inactive_only' => 'غير النشطين فقط',
        'email_verified' => 'تأكيد البريد الإلكتروني',
        'verified_only' => 'المؤكدون فقط',
        'unverified_only' => 'غير المؤكدين فقط',
    ],

    'tabs' => [
        'all' => 'جميع المستخدمين',
    ],
    'roles' => [
        'super_admin' => 'المشرف العام',
        'admin' => 'المشرف',
        'editor' => 'محرر',
        'full_mcp' => 'وصول MCP كامل',
        'content_manager' => 'مدير المحتوى',
        'judge' => 'المحكم',
        'reviewer' => 'المراجع',
    ],
];
