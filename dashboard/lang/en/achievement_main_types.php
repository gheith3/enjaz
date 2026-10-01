<?php

declare(strict_types=1);

/**
 * Achievement main types resource translations (English).
 *
 * Usage: __('achievement_main_types.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Main Type',
    'model_label_plural' => 'Main Types',

    'form' => [
        'sections' => [
            'titles' => 'Titles',
            'appearance' => 'Appearance',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'icon' => 'Icon',
            'show_on_home' => 'Show on Home',
            'is_published' => 'Published',
        ],
        'placeholders' => [],
        'helpers' => [
            'slug' => 'Auto-generated from the Arabic title. Edit it if the generated value is empty.',
            'show_on_home' => 'Display this main type on the public home page.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'titles' => 'Titles',
            'appearance' => 'Appearance',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'icon' => 'Icon',
            'show_on_home' => 'Show on Home',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'icon' => 'Icon',
            'sub_types_count' => 'Sub Types',
            'show_on_home' => 'Show on Home',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'is_published' => 'Published',
        'show_on_home' => 'Show on Home',
        'all' => 'All',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
        'shown_on_home' => 'Shown on home',
        'hidden_from_home' => 'Hidden from home',
    ],
];
