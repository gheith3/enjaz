<?php

declare(strict_types=1);

/**
 * Organizations resource translations (English).
 *
 * Usage: __('organizations.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Organization',
    'model_label_plural' => 'Organizations',

    'form' => [
        'sections' => [
            'identity' => 'Identity',
            'about' => 'About',
            'branding' => 'Branding',
            'manager' => 'Manager',
            'links' => 'Links',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'organization_type' => 'Organization Type',
            'name_ar' => 'Name (Arabic)',
            'name_en' => 'Name (English)',
            'description_ar' => 'Description (Arabic)',
            'description_en' => 'Description (English)',
            'logo' => 'Logo',
            'logo_white' => 'White Logo',
            'manager_name_ar' => 'Manager Name (Arabic)',
            'manager_name_en' => 'Manager Name (English)',
            'manager_message_ar' => 'Manager Message (Arabic)',
            'manager_message_en' => 'Manager Message (English)',
            'manager_image' => 'Manager Image',
            'website_url' => 'Website URL',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'name_ar' => 'اسم المؤسسة',
            'name_en' => 'Organization name',
            'website_url' => 'https://example.com',
        ],
        'helpers' => [
            'logo' => 'Maximum size 280×190, displayed on a white card',
            'logo_white' => 'White version of the logo, used on colored headers',
        ],
    ],

    'table' => [
        'columns' => [
            'logo' => 'Logo',
            'name' => 'Name',
            'website_url' => 'Website',
            'is_published' => 'Published',
            'sort_order' => 'Sort Order',
        ],
    ],

    'filters' => [
        'type' => 'Organization Type',
        'is_published' => 'Publishing Status',
        'all' => 'All',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'Identity',
            'about' => 'About',
            'branding' => 'Branding',
            'manager' => 'Manager',
            'links' => 'Links',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'organization_type' => 'Organization Type',
            'name_ar' => 'Name (Arabic)',
            'name_en' => 'Name (English)',
            'description_ar' => 'Description (Arabic)',
            'description_en' => 'Description (English)',
            'logo' => 'Logo',
            'logo_white' => 'White Logo',
            'manager_name_ar' => 'Manager Name (Arabic)',
            'manager_name_en' => 'Manager Name (English)',
            'manager_message_ar' => 'Manager Message (Arabic)',
            'manager_message_en' => 'Manager Message (English)',
            'manager_image' => 'Manager Image',
            'website_url' => 'Website URL',
            'is_published' => 'Published',
        ],
    ],
];
