<?php

return [
    // Common translations
    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'view' => 'View',
        'restore' => 'Restore',
    ],
    'dates' => [
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ],
    'messages' => [
        'success' => 'Success',
        'error' => 'Error',
        'confirm_delete' => 'Are you sure you want to delete?',
    ],

    // Achievement related translations
    'achievements' => [
        'resource' => [
            'label' => 'Achievement',
            'plural_label' => 'Achievements',
        ],
        'sections' => [
            'owners' => 'Owners',
            'categories' => 'Categories',
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'media' => 'Media',
        ],
        'fields' => [
            'organizations' => 'Organizations',
            'public_figures' => 'Public Figures',
            'achievement_scope' => 'Achievement Scope',
            'achievement_main_type' => 'Achievement Main Type',
            'sub_achievement_type' => 'Sub Achievement Type',
            'title' => 'Title',
            'description' => 'Description',
            'cover_image' => 'Cover Image',
            'video_url' => 'Video URL',
        ],
        'table' => [
            'scope' => 'Scope',
            'main_type' => 'Main Type',
            'sub_type' => 'Sub Type',
        ],
    ],

    'achievement_scopes' => [
        'resource' => [
            'label' => 'Achievement Scope',
            'plural_label' => 'Achievement Scopes',
        ],
        'sections' => [
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'icon' => 'Icon',
        ],
        'table' => [
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
    ],

    'contact_us_messages' => [
        'resource' => [
            'label' => 'Contact Message',
            'plural_label' => 'Contact Messages',
        ],
        'fields' => [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'subject' => 'Subject',
            'message' => 'Message',
            'status' => 'Status',
        ],
        'table' => [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'subject' => 'Subject',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'statuses' => [
            'new' => 'New',
            'read' => 'Read',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ],
    ],

    'image_media' => [
        'resource' => [
            'label' => 'Image Media',
            'plural_label' => 'Image Media',
        ],
        'sections' => [
            'basic_details' => 'Basic Details',
            'media' => 'Media',
            'content' => 'Content',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'Image',
            'alt_text' => 'Alt Text',
            'type' => 'Type',
            'status' => 'Status',
        ],
        'table' => [
            'title' => 'Title',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'types' => [
            'slider' => 'Slider',
            'gallery' => 'Gallery',
            'background' => 'Background',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ],
    ],

    'landing_images' => [
        'resource' => [
            'label' => 'Landing Image',
            'plural_label' => 'Landing Images',
        ],
        'sections' => [
            'basic_details' => 'Basic Details',
            'media' => 'Media',
            'content' => 'Content',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'Image',
            'alt_text' => 'Alt Text',
            'position' => 'Position',
            'status' => 'Status',
        ],
        'table' => [
            'title' => 'Title',
            'position' => 'Position',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'positions' => [
            'header' => 'Header',
            'about' => 'About',
            'services' => 'Services',
            'footer' => 'Footer',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ],
    ],

    'organizations' => [
        'resource' => [
            'label' => 'Organization',
            'plural_label' => 'Organizations',
        ],
        'sections' => [
            'basic_details' => 'Basic Details',
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'media' => 'Media',
            'social_media' => 'Social Media',
        ],
        'fields' => [
            'name_ar' => 'Arabic Name',
            'name_en' => 'English Name',
            'description_ar' => 'Arabic Description',
            'description_en' => 'English Description',
            'logo' => 'Logo',
            'website' => 'Website',
            'email' => 'Email',
            'phone' => 'Phone',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'status' => 'Status',
        ],
        'table' => [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'statuses' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'organization_types' => [
        'resource' => [
            'label' => 'Organization Type',
            'plural_label' => 'Organization Types',
        ],
        'sections' => [
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'media' => 'Media',
        ],
        'fields' => [
            'name_ar' => 'Arabic Name',
            'name_en' => 'English Name',
            'description_ar' => 'Arabic Description',
            'description_en' => 'English Description',
            'icon' => 'Icon',
            'status' => 'Status',
        ],
        'table' => [
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'statuses' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'public_figures' => [
        'resource' => [
            'label' => 'Public Figure',
            'plural_label' => 'Public Figures',
        ],
        'sections' => [
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'media' => 'Media',
            'basic_details' => 'Basic Details',
        ],
        'fields' => [
            'name_ar' => 'Arabic Name',
            'name_en' => 'English Name',
            'position_ar' => 'Arabic Position',
            'position_en' => 'English Position',
            'description_ar' => 'Arabic Description',
            'description_en' => 'English Description',
            'image' => 'Image',
            'email' => 'Email',
            'phone' => 'Phone',
            'status' => 'Status',
        ],
        'table' => [
            'name' => 'Name',
            'position' => 'Position',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'statuses' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'publication_media' => [
        'resource' => [
            'label' => 'Publication Media',
            'plural_label' => 'Publication Media',
        ],
        'sections' => [
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'media' => 'Media',
            'basic_details' => 'Basic Details',
            'content' => 'Content',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'file' => 'File',
            'cover_image' => 'Cover Image',
            'type' => 'Type',
            'status' => 'Status',
            'is_active' => 'Active Status',
            'url' => 'URL',
        ],
        'table' => [
            'title' => 'Title',
            'type' => 'Type',
            'status' => 'Status',
            'publish_date' => 'Publish Date',
            'created_at' => 'Created At',
            'is_active' => 'Active',
            'url' => 'URL',
        ],
        'types' => [
            'book' => 'Book',
            'magazine' => 'Magazine',
            'report' => 'Report',
            'research' => 'Research',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ],
    ],

    'sub_achievement_types' => [
        'resource' => [
            'label' => 'Sub Achievement Type',
            'plural_label' => 'Sub Achievement Types',
        ],
        'sections' => [
            'arabic_details' => 'Arabic Details',
            'english_details' => 'English Details',
            'basic_details' => 'Basic Details',
        ],
        'fields' => [
            'title_ar' => 'Arabic Title',
            'title_en' => 'English Title',
            'achievement_main_type' => 'Main Achievement Type',
            'status' => 'Status',
        ],
        'table' => [
            'title' => 'Title',
            'main_type' => 'Main Type',
            'status' => 'Status',
            'created_at' => 'Created At',
        ],
        'statuses' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'video_media' => [
        'resource' => [
            'label' => 'Video Media',
            'plural_label' => 'Video Media',
        ],
        'sections' => [
            'basic_details' => 'Basic Details',
            'media' => 'Media',
            'content' => 'Content',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'video_url' => 'Video URL',
            'thumbnail' => 'Thumbnail',
            'type' => 'Type',
            'status' => 'Status',
            'is_active' => 'Active Status',
        ],
        'table' => [
            'title' => 'Title',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'is_active' => 'Active',
        ],
        'types' => [
            'youtube' => 'YouTube',
            'vimeo' => 'Vimeo',
            'local' => 'Local Video',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ],
    ],

    'nav' => [
        'groups' => [
            'achievements' => 'Achievements',
            'media' => 'Media',
            'organizations' => 'Organizations',
            'communications' => 'Communications',
            'types' => 'Types',
            'settings' => 'Settings',
        ],
    ],

    'group' => [
        'media' => 'Media',
        'achievements' => 'Achievements',
        'organizations' => 'Organizations',
        'communications' => 'Communications',
        'types' => 'Types',
        'settings' => 'Settings',
    ],
];
