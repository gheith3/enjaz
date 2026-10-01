<?php

declare(strict_types=1);

/**
 * Users resource translations (English).
 *
 * Usage: __('users.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'User',
    'model_label_plural' => 'Users',

    'form' => [
        'sections' => [
            'personal_information' => 'Personal Information',
            'personal_information_description' => 'User\'s basic information and contact details',
            'security' => 'Security',
            'security_description' => 'Password and authentication settings',
            'user_type' => 'User Type',
            'user_type_description' => 'Define user role and type',
            'status' => 'Status',
            'status_description' => 'Account status settings',
        ],
        'fields' => [
            'email_address' => 'Email Address',
            'profile_photo' => 'Profile Photo',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'email_verified_at' => 'Email Verified At',
            'roles' => 'Roles',
            'status' => 'Status',
        ],
        'placeholders' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+968 9XXX XXXX',
        ],
        'helpers' => [
            'status' => 'Suspended and invited users cannot access the dashboard',
        ],
    ],

    'status' => [
        'active' => 'Active',
        'suspended' => 'Suspended',
    ],

    'infolist' => [
        'sections' => [
            'personal_information' => 'Personal Information',
            'role_access' => 'Role & Access',
            'activity' => 'Activity',
            'profile' => 'Profile',
            'status' => 'Status',
        ],
        'fields' => [
            'email_address' => 'Email Address',
            'user_id' => 'User ID',
            'user_type' => 'User Type',
            'account_created' => 'Account Created',
            'last_updated' => 'Last Updated',
            'email_verified' => 'Email Verified',
            'account_status' => 'Account Status',
            'status' => 'Status',
        ],
        'placeholders' => [
            'not_verified' => 'Not verified',
        ],
        'messages' => [
            'email_copied' => 'Email copied!',
        ],
    ],

    'table' => [
        'columns' => [
            'verified' => 'Verified',
            'joined' => 'Joined',
            'two_factor' => '2FA',
        ],
        'placeholders' => [
            'not_verified' => 'Not verified',
        ],
    ],

    'filters' => [
        'active_status' => 'Active Status',
        'status' => 'Status',
        'all_users' => 'All users',
        'active_only' => 'Active only',
        'inactive_only' => 'Inactive only',
        'email_verified' => 'Email Verified',
        'verified_only' => 'Verified only',
        'unverified_only' => 'Unverified only',
    ],

    'tabs' => [
        'all' => 'All Users',
    ],
    'roles' => [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'content_manager' => 'Content Manager',
        'judge' => 'Judge',
        'reviewer' => 'Reviewer',
    ],
];
