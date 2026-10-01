<?php

declare(strict_types=1);

/**
 * Sub achievement types resource translations (English).
 *
 * Usage: __('sub_achievement_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Sub Type',
    'model_label_plural' => 'Sub Types',

    'form' => [
        'sections' => [
            'classification' => 'Classification',
            'titles' => 'Titles',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'main_type' => 'Main Type',
            'is_published' => 'Published',
        ],
        'placeholders' => [],
        'helpers' => [
            'slug' => 'Auto-generated from the Arabic title. Edit it if the generated value is empty.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'titles' => 'Titles',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'main_type' => 'Main Type',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'main_type' => 'Main Type',
            'achievements_count' => 'Achievements',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'main_type' => 'Main Type',
        'is_published' => 'Published',
        'all' => 'All',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],
];
