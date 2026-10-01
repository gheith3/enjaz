<?php

declare(strict_types=1);

/**
 * Publication media resource translations (English).
 *
 * Usage: __('publication_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Publication',
    'model_label_plural' => 'Publications',

    'form' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'publication' => 'Publication',
            'publication_description' => 'Publication file and cover image',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'file' => 'Publication File',
            'cover_image' => 'Cover Image',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'title_ar' => 'Enter the publication title in Arabic',
            'title_en' => 'Enter the publication title in English',
            'description_ar' => 'Publication description in Arabic',
            'description_en' => 'Publication description in English',
        ],
        'helpers' => [
            'file' => 'PDF files only',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'publication' => 'Publication',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'file' => 'Publication File',
            'cover_image' => 'Cover Image',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'Cover',
            'file' => 'File',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'is_active' => 'Active Status',
        'is_published' => 'Publishing Status',
        'all_publications' => 'All publications',
        'active_only' => 'Active only',
        'inactive_only' => 'Inactive only',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],

];
