<?php

declare(strict_types=1);

/**
 * Video media resource translations (English).
 *
 * Usage: __('video_media.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Video',
    'model_label_plural' => 'Videos',

    'form' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'video' => 'Video',
            'video_description' => 'Video URL, cover image and related public figure',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'url' => 'Video URL',
            'cover_image' => 'Cover Image',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'title_ar' => 'Enter the video title in Arabic',
            'title_en' => 'Enter the video title in English',
            'description_ar' => 'Video description in Arabic',
            'description_en' => 'Video description in English',
            'url' => 'https://youtube.com/watch?v=...',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'video' => 'Video',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'url' => 'Video URL',
            'cover_image' => 'Cover Image',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'Cover',
            'url' => 'URL',
            'public_figure' => 'Public Figure',
            'is_active' => 'Active',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'public_figure' => 'Public Figure',
        'is_active' => 'Active Status',
        'is_published' => 'Publishing Status',
        'all_videos' => 'All videos',
        'active_only' => 'Active only',
        'inactive_only' => 'Inactive only',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
    ],

];
