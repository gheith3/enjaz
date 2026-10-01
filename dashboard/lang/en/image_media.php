<?php

declare(strict_types=1);

/**
 * Image media (photo albums) resource translations (English).
 *
 * Usage: __('image_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Photo Album',
    'model_label_plural' => 'Photo Albums',

    'form' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'album' => 'Album',
            'album_description' => 'Album images and related public figure',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'images' => 'Images',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'title_ar' => 'Enter the album title in Arabic',
            'title_en' => 'Enter the album title in English',
            'description_ar' => 'Album description in Arabic',
            'description_en' => 'Album description in English',
        ],
        'helpers' => [
            'images' => 'Upload multiple images; drag to reorder them',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'album' => 'Album',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'images' => 'Images',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'images_count' => 'Images',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'public_figure' => 'Public Figure',
        'is_active' => 'Active Status',
        'is_published' => 'Publishing Status',
        'all_albums' => 'All albums',
        'active_only' => 'Active only',
        'inactive_only' => 'Inactive only',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],

];
