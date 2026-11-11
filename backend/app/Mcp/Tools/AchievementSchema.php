<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;

/**
 * Input schema of the achievement fields shared by create-achievement and update-achievement.
 */
final class AchievementSchema
{
    /** @return array<string, Type> */
    public static function fields(JsonSchema $schema): array
    {
        return [
            'title_en' => $schema->string()->description('English title.'),
            'scope' => $schema->string()->description('Scope slug; use list-taxonomies.'),
            'main_type' => $schema->string()->description('Main-type (field) slug; use list-taxonomies.'),
            'sub_type' => $schema->string()->description('Not used on this site (there are no sub types); accepted for compatibility and reported in notes. Use discipline_ar / discipline_en.'),
            'discipline_ar' => $schema->string()->description('Arabic specialty shown on the story, e.g. «كرة القدم» (empty = the main type name).'),
            'discipline_en' => $schema->string()->description('English specialty.'),
            'achieved_at' => $schema->string()->description('Date of the achievement, YYYY-MM-DD.'),
            'country_ar' => $schema->string()->description('Country name in Arabic.'),
            'country_en' => $schema->string()->description('Country name in English.'),
            'excerpt_ar' => $schema->string()->description('Short Arabic excerpt (shown on cards).'),
            'excerpt_en' => $schema->string()->description('Short English excerpt.'),
            'description_ar' => $schema->string()->description('Full Arabic story: plain text (blank lines separate paragraphs) or HTML.'),
            'description_en' => $schema->string()->description('Full English story: plain text or HTML.'),
            'video_url' => $schema->string()->description('Optional video URL.'),
        ];
    }
}
