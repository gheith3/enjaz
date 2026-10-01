<?php

declare(strict_types=1);

/**
 * Activities resource translations (English).
 *
 * Usage: __('activities.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Activity Log',
    'model_label_plural' => 'Activity Logs',

    'navigation' => [
        'badge_tooltip' => 'Activities today',
    ],

    'tabs' => [
        'all' => 'All',
        'today' => 'Today',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
    ],

    'events' => [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'login' => 'Login',
        'logout' => 'Logout',
    ],

    'actions' => [
        'view_subject' => 'View Subject',
        'view_batch' => 'View Batch',
    ],

    'infolist' => [
        'sections' => [
            'causer_information' => 'Causer Information',
            'subject_history_timeline' => 'Subject History Timeline',
            'activity_details' => 'Activity Details',
            'additional_properties' => 'Additional Properties',
            'batch_information' => 'Batch Information',
            'timestamps' => 'Timestamps',
        ],
        'fields' => [
            'causer_type' => 'Causer Type',
            'causer_id' => 'Causer ID',
            'causer_name' => 'Causer Name',
            'causer_email' => 'Causer Email',
            'activity_id' => 'Activity ID',
            'log_name' => 'Log Name',
            'event' => 'Event',
            'subject_type' => 'Subject Type',
            'subject_id' => 'Subject ID',
            'subject_name' => 'Subject Name',
            'batch_uuid' => 'Batch UUID',
            'activities_in_batch' => 'Activities in Batch',
        ],
        'placeholders' => [
            'system' => 'System',
            'no_additional_properties' => 'No additional properties',
        ],
    ],

    'table' => [
        'columns' => [
            'event' => 'Event',
            'subject' => 'Subject',
            'caused_by' => 'Caused By',
            'batch' => 'Batch',
        ],
    ],

    'filters' => [
        'log_name' => 'Log Name',
        'event' => 'Event',
        'subject_type' => 'Subject Type',
        'caused_by' => 'Caused By',
        'from' => 'From',
        'until' => 'Until',
        'from_indicator' => 'From :date',
        'until_indicator' => 'Until :date',
        'has_batch' => 'Has Batch',
    ],

];
