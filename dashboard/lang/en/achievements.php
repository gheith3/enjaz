<?php

declare(strict_types=1);

/**
 * Achievements resource translations (English).
 *
 * Usage: __('achievements.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Achievement',
    'model_label_plural' => 'Achievements',

    'form' => [
        'sections' => [
            'classification' => 'Classification',
            'content_ar' => 'Content (Arabic)',
            'content_en' => 'Content (English)',
            'media' => 'Media',
            'details' => 'Details',
            'relations' => 'Relations',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'scope' => 'Scope',
            'main_type' => 'Main Type',
            'sub_type' => 'Sub Type',
            'excerpt_ar' => 'Excerpt (Arabic)',
            'excerpt_en' => 'Excerpt (English)',
            'cover_image' => 'Cover Image',
            'video_url' => 'Video URL',
            'achieved_at' => 'Achieved At',
            'country_ar' => 'Country (Arabic)',
            'country_en' => 'Country (English)',
            'public_figures' => 'Public Figures',
            'organizations' => 'Organizations',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'video_url' => 'https://youtube.com/watch?v=...',
        ],
        'helpers' => [
            'slug' => 'Auto-generated from the Arabic title. Edit it if the generated value is empty.',
            'sub_type' => 'Options depend on the selected main type.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'classification' => 'Classification',
            'content' => 'Content',
            'media' => 'Media',
            'details' => 'Details',
            'relations' => 'Relations',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'scope' => 'Scope',
            'main_type' => 'Main Type',
            'sub_type' => 'Sub Type',
            'excerpt_ar' => 'Excerpt (Arabic)',
            'excerpt_en' => 'Excerpt (English)',
            'cover_image' => 'Cover Image',
            'video_url' => 'Video URL',
            'achieved_at' => 'Achieved At',
            'country_ar' => 'Country (Arabic)',
            'country_en' => 'Country (English)',
            'public_figures' => 'Public Figures',
            'organizations' => 'Organizations',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'Cover',
            'scope' => 'Scope',
            'achieved_at' => 'Achieved At',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'scope' => 'Scope',
        'main_type' => 'Main Type',
        'is_published' => 'Published',
        'all' => 'All',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],
];
