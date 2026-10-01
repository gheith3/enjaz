<?php

declare(strict_types=1);

/**
 * Shared translations for the Filament admin panel (English).
 *
 * Always reuse these keys before creating group-specific ones:
 *   __('common.nav_groups.finance')
 *   __('common.actions.save')
 *   __('common.fields.status')
 */
return [

    'form_builder' => [
        'label' => 'Form Fields',
        'add_field' => 'Add Field',
        'add_option' => 'Add Option',
        'type' => 'Field Type',
        'is_required' => 'Required',
        'is_sensitive' => 'Sensitive',
        'is_sensitive_helper' => 'The answer will be stored encrypted (AES-256-GCM)',
        'options' => 'Options',
        'min' => 'Minimum Value',
        'max' => 'Maximum Value',
        'accepted_types' => 'Accepted File Types',
        'accepted_types_helper' => 'Allowed extensions for this upload. Leave empty to accept any type.',
        'accepted_types_placeholder' => 'e.g. png, svg, pdf',
        'max_size' => 'Maximum File Size',
        'max_size_helper' => 'In megabytes. Leave empty for no limit.',
        'min_length' => 'Minimum Length',
        'max_length' => 'Maximum Length',
        'placeholder_ar' => 'Placeholder (Arabic)',
        'placeholder_en' => 'Placeholder (English)',
        'description_ar' => 'Description (Arabic)',
        'description_en' => 'Description (English)',
        'types' => [
            'text' => 'Short Text',
            'textarea' => 'Long Text',
            'integer' => 'Integer',
            'decimal' => 'Decimal',
            'date' => 'Date',
            'datetime' => 'Date & Time',
            'boolean' => 'Yes / No',
            'select' => 'Single Select',
            'multiselect' => 'Multi Select',
            'url' => 'URL',
            'image' => 'Image',
            'file' => 'File',
        ],
    ],

    'nav_groups' => [
        'content_management' => 'Content Management',
        'awards' => 'Awards',
        'operations' => 'Operations',
        'administration' => 'Administration',
        'settings' => 'Settings',
        'notifications' => 'Notifications',
        'import_audit' => 'Import & Audit',
        'system' => 'System',
    ],

    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'confirm' => 'Confirm',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'print' => 'Print',
        'download' => 'Download',
        'refresh' => 'Refresh',
        'back' => 'Back',
        'submit' => 'Submit',
        'send' => 'Send',
        'add' => 'Add',
        'remove' => 'Remove',
        'restore' => 'Restore',
    ],

    'booleans' => [
        'yes' => 'Yes',
        'no' => 'No',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
    ],

    'fields' => [
        'name' => 'Name',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'phone_number' => 'Phone Number',
        'country_code' => 'Country Code',
        'status' => 'Status',
        'type' => 'Type',
        'date' => 'Date',
        'time' => 'Time',
        'notes' => 'Notes',
        'description' => 'Description',
        'address' => 'Address',
        'city' => 'City',
        'country' => 'Country',
        'total' => 'Total',
        'subtotal' => 'Subtotal',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'amount' => 'Amount',
        'currency' => 'Currency',
        'image' => 'Image',
        'images' => 'Images',
        'attachments' => 'Attachments',
        'code' => 'Code',
        'slug' => 'Slug',
        'category' => 'Category',
        'customer' => 'Customer',
        'order' => 'Order',
        'item' => 'Item',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'rules' => 'Rules',
        'deleted_at' => 'Deleted At',
        'sort_order' => 'Sort Order',
        'actions' => 'Actions',
        'from' => 'From',
        'to' => 'To',
    ],

    'messages' => [
        'saved_successfully' => 'Saved successfully.',
        'deleted_successfully' => 'Deleted successfully.',
        'operation_failed' => 'The operation failed. Please try again.',
        'are_you_sure' => 'Are you sure?',
        'this_action_cannot_be_undone' => 'This action cannot be undone.',
        'no_records_found' => 'No records found.',
        'loading' => 'Loading...',
    ],

    'helpers' => [
        'slug_auto' => 'Leave empty to auto-generate from the English name',
    ],

    'placeholders' => [
        'search' => 'Search...',
        'select_option' => 'Select an option',
        'all' => 'All',
        'not_provided' => 'Not provided',
        'not_available' => 'N/A',
        'guest' => 'Guest',
        'unassigned' => 'Unassigned',
    ],

    /*
     * Shared labels for models using the HasTranslations trait.
     * Used by App\Filament\Components\Translatable{Form,Table,Infolist}.
     * Add keys here for any new translatable field (e.g. 'name', 'name_ar', ...).
     */
    'translations' => [
        'ar' => 'Arabic',
        'en' => 'English',
        'title' => 'Title',
        'description' => 'Description',
        'name' => 'Name',
        'name_ar' => 'Name (Arabic)',
        'name_en' => 'Name (English)',
        'title_ar' => 'Title (Arabic)',
        'title_en' => 'Title (English)',
        'description_ar' => 'Description (Arabic)',
        'description_en' => 'Description (English)',
        'question' => 'Question',
        'question_ar' => 'Question (Arabic)',
        'question_en' => 'Question (English)',
        'answer' => 'Answer',
        'answer_ar' => 'Answer (Arabic)',
        'answer_en' => 'Answer (English)',
    ],

];
