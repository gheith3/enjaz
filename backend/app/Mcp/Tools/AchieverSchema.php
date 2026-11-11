<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;

/**
 * Input schema of the achiever fields shared by create-achiever and update-achiever.
 */
final class AchieverSchema
{
    /** @return array<string, Type> */
    public static function fields(JsonSchema $schema): array
    {
        return [
            'name_en' => $schema->string()->description('English name.'),
            'main_type' => $schema->string()->description('Main-type (field) slug; use list-taxonomies.'),
            'gender' => $schema->string()->enum(['m', 'f'])->description('m or f (used for «منجز/منجزة الأسبوع» and wording). Defaults to m on create.'),
            'headline_ar' => $schema->string()->description('Arabic headline (card subtitle).'),
            'headline_en' => $schema->string()->description('English headline.'),
            'description_ar' => $schema->string()->description('Arabic short bio (ID card).'),
            'description_en' => $schema->string()->description('English short bio.'),
            'story_ar' => $schema->string()->description('Arabic success story («قصتي»).'),
            'story_en' => $schema->string()->description('English success story.'),
            'linkedin_url' => $schema->string()->description('LinkedIn profile URL.'),
            'twitter_url' => $schema->string()->description('Twitter/X profile URL.'),
            'instagram_url' => $schema->string()->description('Instagram profile URL.'),
            'facebook_url' => $schema->string()->description('Facebook profile URL.'),
            'whatsapp_number' => $schema->string()->description('WhatsApp number.'),
            'email' => $schema->string()->description('Not stored on this site (accepted for compatibility, reported in notes).'),
            'phone' => $schema->string()->description('Not stored on this site (accepted for compatibility, reported in notes).'),
        ];
    }
}
