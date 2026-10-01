<?php

declare(strict_types=1);

/**
 * Shared translations for the Filament admin panel (Arabic).
 *
 * Always reuse these keys before creating group-specific ones:
 *   __('common.nav_groups.finance')
 *   __('common.actions.save')
 *   __('common.fields.status')
 */
return [

    'form_builder' => [
        'label' => 'حقول النموذج',
        'add_field' => 'إضافة حقل',
        'add_option' => 'إضافة خيار',
        'type' => 'نوع الحقل',
        'is_required' => 'إلزامي',
        'is_sensitive' => 'حساس',
        'is_sensitive_helper' => 'سيتم تخزين الإجابة مشفرة (AES-256-GCM)',
        'options' => 'الخيارات',
        'min' => 'الحد الأدنى',
        'max' => 'الحد الأعلى',
        'accepted_types' => 'أنواع الملفات المقبولة',
        'accepted_types_helper' => 'الامتدادات المسموح بها لهذا الرفع. اتركه فارغًا لقبول أي نوع.',
        'accepted_types_placeholder' => 'مثال: png, svg, pdf',
        'max_size' => 'الحجم الأقصى للملف',
        'max_size_helper' => 'بالميجابايت. اتركه فارغًا لعدم وجود حد.',
        'min_length' => 'الحد الأدنى للطول',
        'max_length' => 'الحد الأقصى للطول',
        'placeholder_ar' => 'النص التوضيحي (بالعربية)',
        'placeholder_en' => 'النص التوضيحي (بالإنجليزية)',
        'description_ar' => 'الوصف (بالعربية)',
        'description_en' => 'الوصف (بالإنجليزية)',
        'types' => [
            'text' => 'نص قصير',
            'textarea' => 'نص طويل',
            'integer' => 'رقم صحيح',
            'decimal' => 'رقم عشري',
            'date' => 'تاريخ',
            'datetime' => 'تاريخ ووقت',
            'boolean' => 'نعم / لا',
            'select' => 'اختيار واحد',
            'multiselect' => 'اختيار متعدد',
            'url' => 'رابط',
            'image' => 'صورة',
            'file' => 'ملف',
        ],
    ],

    'nav_groups' => [
        'content_management' => 'إدارة المحتوى',
        'awards' => 'الجوائز',
        'operations' => 'العمليات',
        'administration' => 'الإدارة',
        'settings' => 'الإعدادات',
        'notifications' => 'الإشعارات',
        'import_audit' => 'الاستيراد والتدقيق',
        'system' => 'النظام',
    ],

    'actions' => [
        'create' => 'إنشاء',
        'edit' => 'تعديل',
        'view' => 'عرض',
        'delete' => 'حذف',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'close' => 'إغلاق',
        'confirm' => 'تأكيد',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'print' => 'طباعة',
        'download' => 'تنزيل',
        'refresh' => 'تحديث',
        'back' => 'رجوع',
        'submit' => 'إرسال',
        'send' => 'إرسال',
        'add' => 'إضافة',
        'remove' => 'إزالة',
        'restore' => 'استعادة',
    ],

    'booleans' => [
        'yes' => 'نعم',
        'no' => 'لا',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'enabled' => 'مفعّل',
        'disabled' => 'معطّل',
    ],

    'fields' => [
        'name' => 'الاسم',
        'full_name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'phone_number' => 'رقم الهاتف',
        'country_code' => 'رمز الدولة',
        'status' => 'الحالة',
        'type' => 'النوع',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'notes' => 'ملاحظات',
        'description' => 'الوصف',
        'address' => 'العنوان',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'total' => 'الإجمالي',
        'subtotal' => 'المجموع الفرعي',
        'quantity' => 'الكمية',
        'price' => 'السعر',
        'amount' => 'المبلغ',
        'currency' => 'العملة',
        'image' => 'الصورة',
        'images' => 'الصور',
        'attachments' => 'المرفقات',
        'code' => 'الرمز',
        'slug' => 'الرابط المختصر',
        'category' => 'الفئة',
        'customer' => 'العميل',
        'order' => 'الطلب',
        'item' => 'الصنف',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'rules' => 'القواعد',
        'deleted_at' => 'تاريخ الحذف',
        'sort_order' => 'ترتيب الظهور',
        'actions' => 'الإجراءات',
        'from' => 'من',
        'to' => 'إلى',
    ],

    'messages' => [
        'saved_successfully' => 'تم الحفظ بنجاح.',
        'deleted_successfully' => 'تم الحذف بنجاح.',
        'operation_failed' => 'فشلت العملية. يرجى المحاولة مرة أخرى.',
        'are_you_sure' => 'هل أنت متأكد؟',
        'this_action_cannot_be_undone' => 'لا يمكن التراجع عن هذا الإجراء.',
        'no_records_found' => 'لا توجد سجلات.',
        'loading' => 'جارٍ التحميل...',
    ],

    'helpers' => [
        'slug_auto' => 'اتركه فارغًا لتوليده تلقائيًا من الاسم الإنجليزي',
    ],

    'placeholders' => [
        'search' => 'ابحث...',
        'select_option' => 'اختر خيارًا',
        'all' => 'الكل',
        'not_provided' => 'غير متوفر',
        'not_available' => 'غير متاح',
        'guest' => 'زائر',
        'unassigned' => 'غير مسند',
    ],

    /*
     * Shared labels for models using the HasTranslations trait.
     * Used by App\Filament\Components\Translatable{Form,Table,Infolist}.
     * Add keys here for any new translatable field (e.g. 'name', 'name_ar', ...).
     */
    'translations' => [
        'ar' => 'العربية',
        'en' => 'الإنجليزية',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'name' => 'الاسم',
        'name_ar' => 'الاسم (بالعربية)',
        'name_en' => 'الاسم (بالإنجليزية)',
        'title_ar' => 'العنوان (بالعربية)',
        'title_en' => 'العنوان (بالإنجليزية)',
        'description_ar' => 'الوصف (بالعربية)',
        'description_en' => 'الوصف (بالإنجليزية)',
        'question' => 'السؤال',
        'question_ar' => 'السؤال (بالعربية)',
        'question_en' => 'السؤال (بالإنجليزية)',
        'answer' => 'الإجابة',
        'answer_ar' => 'الإجابة (بالعربية)',
        'answer_en' => 'الإجابة (بالإنجليزية)',
    ],

];
