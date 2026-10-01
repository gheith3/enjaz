<?php

declare(strict_types=1);

/**
 * Testimonials resource translations (English).
 *
 * Usage: __('testimonials.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Testimonial',
    'model_label_plural' => 'Testimonials',

    'form' => [
        'sections' => [
            'person' => 'Person',
            'person_description' => 'Name and position of the person giving the testimonial',
            'quote' => 'Quote',
            'quote_description' => 'The testimonial text',
            'photo' => 'Photo',
            'photo_description' => 'Square avatar-style photo',
            'publishing' => 'Publishing',
            'publishing_description' => 'Slug, visibility and ordering',
        ],
        'fields' => [
            'person_name_ar' => 'Person Name (Arabic)',
            'person_name_en' => 'Person Name (English)',
            'position_ar' => 'Position (Arabic)',
            'position_en' => 'Position (English)',
            'quote_ar' => 'Quote (Arabic)',
            'quote_en' => 'Quote (English)',
            'photo' => 'Photo',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'person_name_ar' => 'مثال: د. سالم الحارثي',
            'person_name_en' => 'e.g. Dr. Salim Al Harthi',
            'position_ar' => 'مثال: رئيس مجلس الإدارة',
            'position_en' => 'e.g. Chairman of the Board',
        ],
        'helpers' => [
            'photo' => 'Square photo — aspect ratio 252:252',
        ],
    ],

    'table' => [
        'columns' => [
            'person_name' => 'Person',
            'quote' => 'Quote',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'is_published' => 'Published',
    ],

    'infolist' => [
        'sections' => [
            'person' => 'Person',
            'quote' => 'Quote',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'person_name_ar' => 'Person Name (Arabic)',
            'person_name_en' => 'Person Name (English)',
            'position_ar' => 'Position (Arabic)',
            'position_en' => 'Position (English)',
            'quote_ar' => 'Quote (Arabic)',
            'quote_en' => 'Quote (English)',
            'is_published' => 'Published',
        ],
    ],
];
