<?php

declare(strict_types=1);

/**
 * Achievement scopes resource translations (English).
 *
 * Usage: __('achievement_scopes.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Scope',
    'model_label_plural' => 'Scopes',

    'form' => [
        'sections' => [
            'titles' => 'Titles',
            'publishing' => 'Publishing',
        ],
        'fields' => [
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
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'achievements_count' => 'Achievements',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [],
];
