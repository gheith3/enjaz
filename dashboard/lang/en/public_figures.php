<?php

declare(strict_types=1);

/**
 * Public Figures resource translations (English).
 *
 * Usage: __('public_figures.model_label')
 * Reuse __('common.*') keys whenever a shared key exists.
 */
return [

    'model_label' => 'Public Figure',
    'model_label_plural' => 'Public Figures',

    'form' => [
        'sections' => [
            'identity' => 'Identity',
            'identity_description' => 'Name and one-line professional headline',
            'biography' => 'Biography',
            'biography_description' => 'Short description and full achievement story',
            'images' => 'Images',
            'images_description' => 'Profile, tile, cutout and banner images',
            'classification' => 'Classification',
            'classification_description' => 'Main achievement type this figure belongs to',
            'flags' => 'Flags',
            'flags_description' => 'Featuring and home carousel visibility',
            'social_contact' => 'Social & Contact',
            'social_contact_description' => 'Social media profiles and contact details',
            'publishing' => 'Publishing',
            'publishing_description' => 'Slug, visibility and ordering',
        ],
        'fields' => [
            'headline_ar' => 'Headline (Arabic)',
            'headline_en' => 'Headline (English)',
            'description_ar' => 'Description (Arabic)',
            'description_en' => 'Description (English)',
            'story_ar' => 'Story (Arabic)',
            'story_en' => 'Story (English)',
            'image' => 'Profile Image',
            'tile_image' => 'Tile Image',
            'cutout_image' => 'Cutout Image',
            'banner_image' => 'Banner Image',
            'main_type' => 'Main Achievement Type',
            'is_featured' => 'Featured',
            'in_home_carousel' => 'In Home Carousel',
            'linkedin_url' => 'LinkedIn URL',
            'twitter_url' => 'X (Twitter) URL',
            'instagram_url' => 'Instagram URL',
            'facebook_url' => 'Facebook URL',
            'whatsapp_number' => 'WhatsApp Number',
            'is_published' => 'Published',
        ],
        'placeholders' => [
            'name_ar' => 'مثال: أحمد بن سالم',
            'name_en' => 'e.g. Ahmed Al Salmi',
            'headline_ar' => 'مثال: أول عماني يصل قمة إفرست',
            'headline_en' => 'e.g. First Omani to summit Everest',
            'linkedin_url' => 'https://linkedin.com/in/username',
            'twitter_url' => 'https://x.com/username',
            'instagram_url' => 'https://instagram.com/username',
            'facebook_url' => 'https://facebook.com/username',
            'whatsapp_number' => '+968 9XXX XXXX',
            'email' => 'name@example.com',
            'phone' => '+968 9XXX XXXX',
        ],
        'helpers' => [
            'tile_image' => 'Used in listing cards — aspect ratio 351:427',
            'cutout_image' => 'Transparent PNG — aspect ratio 848:477',
            'is_featured' => 'Highlight this figure on featured sections',
            'in_home_carousel' => 'Show this figure in the home page carousel',
        ],
    ],

    'table' => [
        'columns' => [
            'main_type' => 'Main Type',
            'achievements_count' => 'Achievements',
            'is_featured' => 'Featured',
            'in_home_carousel' => 'Carousel',
            'is_published' => 'Published',
        ],
    ],

    'filters' => [
        'main_type' => 'Main Achievement Type',
        'is_featured' => 'Featured',
        'is_published' => 'Published',
    ],

    'infolist' => [
        'sections' => [
            'identity' => 'Identity',
            'biography' => 'Biography',
            'images' => 'Images',
            'flags' => 'Flags',
            'social_contact' => 'Social & Contact',
            'publishing' => 'Publishing',
        ],
        'fields' => [
            'headline_ar' => 'Headline (Arabic)',
            'headline_en' => 'Headline (English)',
            'description_ar' => 'Description (Arabic)',
            'description_en' => 'Description (English)',
            'story_ar' => 'Story (Arabic)',
            'story_en' => 'Story (English)',
            'image' => 'Profile Image',
            'tile_image' => 'Tile Image',
            'cutout_image' => 'Cutout Image',
            'banner_image' => 'Banner Image',
            'main_type' => 'Main Achievement Type',
            'is_featured' => 'Featured',
            'in_home_carousel' => 'In Home Carousel',
            'is_published' => 'Published',
            'linkedin_url' => 'LinkedIn URL',
            'twitter_url' => 'X (Twitter) URL',
            'instagram_url' => 'Instagram URL',
            'facebook_url' => 'Facebook URL',
            'whatsapp_number' => 'WhatsApp Number',
        ],
    ],
];
