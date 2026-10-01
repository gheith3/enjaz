<?php

declare(strict_types=1);

/**
 * Achievement scopes resource translations (Arabic).
 *
 * Usage: __('achievement_scopes.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'نطاق',
    'model_label_plural' => 'النطاقات',

    'form' => [
        'sections' => [
            'titles' => 'العناوين',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'is_published' => 'منشور',
        ],
        'placeholders' => [],
        'helpers' => [
            'slug' => 'يُولَّد تلقائيًا من العنوان العربي. عدّله إذا كانت القيمة المولدة فارغة.',
        ],
    ],

    'infolist' => [
        'sections' => [
            'titles' => 'العناوين',
            'publishing' => 'النشر',
        ],
        'fields' => [
            'is_published' => 'منشور',
        ],
    ],

    'table' => [
        'columns' => [
            'achievements_count' => 'الإنجازات',
            'is_published' => 'منشور',
        ],
    ],

    'filters' => [],
];
