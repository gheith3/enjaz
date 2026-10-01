<?php

declare(strict_types=1);

/**
 * News resource translations (English).
 *
 * Usage: __('news.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'News',
    'model_label_plural' => 'News',

    'form' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'details' => 'Cover & Details',
            'details_description' => 'Cover image, publishing date and related organization',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'excerpt_ar' => 'Excerpt (Arabic)',
            'excerpt_en' => 'Excerpt (English)',
            'body_ar' => 'Body (Arabic)',
            'body_en' => 'Body (English)',
            'cover_image' => 'Cover Image',
            'published_at' => 'Published At',
            'organization' => 'Organization',
            'show_on_about' => 'Show on About Page',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'title_ar' => 'Enter the news title in Arabic',
            'title_en' => 'Enter the news title in English',
            'excerpt_ar' => 'Short summary in Arabic',
            'excerpt_en' => 'Short summary in English',
            'body_ar' => 'Full news body in Arabic',
            'body_en' => 'Full news body in English',
        ],
        'helpers' => [
            'cover_image' => 'Recommended aspect ratio 345:208',
            'show_on_about' => 'Display this news item on the public about page',
        ],
    ],

    'infolist' => [
        'sections' => [
            'content_ar' => 'Arabic Content',
            'content_en' => 'English Content',
            'details' => 'Cover & Details',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'excerpt_ar' => 'Excerpt (Arabic)',
            'excerpt_en' => 'Excerpt (English)',
            'body_ar' => 'Body (Arabic)',
            'body_en' => 'Body (English)',
            'cover_image' => 'Cover Image',
            'published_at' => 'Published At',
            'organization' => 'Organization',
            'show_on_about' => 'Show on About Page',
            'is_published' => 'Published',
        ],
    ],

    'table' => [
        'columns' => [
            'cover' => 'Cover',
            'organization' => 'Organization',
            'published_at' => 'Published At',
            'is_published' => 'Published',
            'show_on_about' => 'On About',
        ],
    ],

    'filters' => [
        'organization' => 'Organization',
        'is_published' => 'Publishing Status',
        'all_news' => 'All news',
        'published_only' => 'Published only',
        'unpublished_only' => 'Unpublished only',
        'published_range' => 'Publishing Date',
        'published_from' => 'Published From',
        'published_until' => 'Published Until',
    ],

];
