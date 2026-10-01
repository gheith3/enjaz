<?php

declare(strict_types=1);

/**
 * Team Members resource translations (English).
 *
 * Usage: __('team_members.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Team Member',
    'model_label_plural' => 'Team Members',

    'form' => [
        'sections' => [
            'member' => 'Member',
            'member_description' => 'Name and position of the team member',
            'photo' => 'Photo',
            'photo_description' => 'Team member portrait photo',
            'publishing' => 'Publishing',
            'publishing_description' => 'Slug, visibility and ordering',
        ],
        'fields' => [
            'position_ar' => 'Position (Arabic)',
            'position_en' => 'Position (English)',
            'photo' => 'Photo',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'name_ar' => 'مثال: محمد البلوشي',
            'name_en' => 'e.g. Mohammed Al Balushi',
            'position_ar' => 'مثال: مدير المشروع',
            'position_en' => 'e.g. Project Manager',
        ],
        'helpers' => [
            'photo' => 'Portrait photo — aspect ratio 333:359',
        ],
    ],

    'table' => [
        'columns' => [
            'position_en' => 'Position (English)',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'is_published' => 'Published',
    ],

    'infolist' => [
        'sections' => [
            'member' => 'Member',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'position_ar' => 'Position (Arabic)',
            'position_en' => 'Position (English)',
            'is_published' => 'Published',
        ],
    ],
];
