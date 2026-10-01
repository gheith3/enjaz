<?php

declare(strict_types=1);

/**
 * Contact resource translations (English).
 *
 * Usage: __('contact.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Contact Message',
    'model_label_plural' => 'Contact Messages',

    'status' => [
        'new' => 'New',
        'read' => 'Read',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ],

    'navigation' => [
        'badge_tooltip' => 'New contact messages',
    ],

    'table' => [
        'columns' => [
            'name' => 'Name',
            'message' => 'Message',
            'status' => 'Status',
            'ip_address' => 'IP Address',
            'created_at' => 'Received At',
        ],
    ],

    'infolist' => [
        'sections' => [
            'sender_information' => 'Sender Information',
            'message' => 'Message',
            'timestamps' => 'Timestamps',
        ],
        'fields' => [
            'name' => 'Name',
            'email' => 'Email',
            'ip_address' => 'IP Address',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
    ],

    'filters' => [
        'status' => 'Status',
        'from' => 'From',
        'until' => 'Until',
        'from_indicator' => 'From :date',
        'until_indicator' => 'Until :date',
    ],
];
