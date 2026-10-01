<?php

declare(strict_types=1);

/**
 * Organization types resource translations (English).
 *
 * Usage: __('organization_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Organization Type',
    'model_label_plural' => 'Organization Types',

    'form' => [
        'sections' => [
            'identity' => 'Identity',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'title_ar' => 'Title (Arabic)',
            'title_en' => 'Title (English)',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'title_ar' => 'نوع المؤسسة',
            'title_en' => 'Organization type',
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'Title',
            'organizations_count' => 'Organizations',
            'is_published' => 'Published',
            'sort_order' => 'Sort Order',
        ],
    ],

    'filters' => [
        'is_published' => 'Publishing Status',
        'all' => 'All',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'Identity',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'title_ar' => 'Title (Arabic)',
            'title_en' => 'Title (English)',
            'organizations_count' => 'Organizations',
            'is_published' => 'Published',
        ],
    ],
];
