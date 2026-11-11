<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\CreateAchievement;
use App\Mcp\Tools\CreateAchiever;
use App\Mcp\Tools\DeleteAchievement;
use App\Mcp\Tools\DeleteAchiever;
use App\Mcp\Tools\GetAchievement;
use App\Mcp\Tools\GetAchiever;
use App\Mcp\Tools\ListTaxonomies;
use App\Mcp\Tools\SearchAchievements;
use App\Mcp\Tools\SearchAchievers;
use App\Mcp\Tools\UpdateAchievement;
use App\Mcp\Tools\UpdateAchiever;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Organization;
use App\Models\Scope;

/**
 * The content tools with the old argument names, mapped onto the new schema.
 */
class McpContentToolsTest extends McpTestCase
{
    public function test_list_taxonomies_keeps_the_old_shape(): void
    {
        Scope::factory()->create(['slug' => 'international', 'name' => ['ar' => 'دولي', 'en' => 'International']]);
        Field::factory()->create(['slug' => 'sports', 'name' => ['ar' => 'الرياضة']]);

        $content = $this->content(ListTaxonomies::class);

        $this->assertSame([['id' => Scope::query()->value('id'), 'slug' => 'international', 'title_ar' => 'دولي', 'title_en' => 'International']], $content['scopes']);
        $this->assertSame([['id' => Field::query()->value('id'), 'slug' => 'sports', 'title_ar' => 'الرياضة', 'title_en' => null, 'sub_types' => []]], $content['main_types']);
    }

    public function test_an_achievement_is_created_searched_read_updated_and_soft_deleted(): void
    {
        Scope::factory()->create(['slug' => 'gulf']);
        Field::factory()->create(['slug' => 'sports']);
        Field::factory()->create(['slug' => 'culture']);
        [$first, $second] = Achiever::factory()->count(2)->create();
        $organization = Organization::factory()->create();

        $created = $this->content(CreateAchievement::class, [
            'title_ar' => 'كأس الخليج', 'title_en' => 'Gulf Cup', 'scope' => 'gulf', 'main_type' => 'sports', 'sub_type' => 'football',
            'description_ar' => "الفقرة الأولى.\n\nالفقرة الثانية.", 'country_en' => 'Oman', 'achieved_at' => '2025-01-04',
            'achievers' => [$second->slug, $first->slug], 'organizations' => [$organization->slug],
        ]);

        $this->assertTrue($created['created']);
        $this->assertSame('gulf-cup', $created['achievement']['slug']);
        $this->assertFalse($created['achievement']['is_published'], 'drafts by default, as before');
        $this->assertSame('gulf', $created['achievement']['scope']['slug']);
        $this->assertSame('sports', $created['achievement']['main_type']['slug']);
        $this->assertNull($created['achievement']['sub_type']);
        $this->assertStringContainsString('sub_type', $created['notes'][0]);
        $this->assertSame([$second->slug, $first->slug], $created['achievement']['achievers']);
        $this->assertSame([$organization->slug], $created['achievement']['organizations']);
        $this->assertNull($created['achievement']['images']['cover']);

        $achievement = Achievement::query()->where('slug', 'gulf-cup')->sole();
        $this->assertSame('<p>الفقرة الأولى.</p><p>الفقرة الثانية.</p>', $achievement->getTranslation('body', 'ar'));
        $this->assertSame('Oman', $achievement->getTranslation('country', 'en'));
        $this->assertTrue((bool) $achievement->achievers()->where('achievers.id', $second->id)->first()->pivot->is_primary);

        $search = $this->content(SearchAchievements::class, ['q' => 'الخليج', 'main_type' => 'sports', 'published' => false]);
        $this->assertSame(1, $search['total']);
        $this->assertSame('gulf-cup', $search['achievements'][0]['slug']);
        $this->assertSame(0, $this->content(SearchAchievements::class, ['q' => 'الخليج', 'published' => true])['total']);

        $this->assertSame('Gulf Cup', $this->content(GetAchievement::class, ['identifier' => (string) $achievement->id])['achievement']['title_en']);

        $updated = $this->content(UpdateAchievement::class, [
            'slug' => 'gulf-cup', 'new_slug' => 'gulf-cup-2025', 'title_en' => null, 'main_type' => 'culture', 'is_published' => true,
            'discipline_ar' => 'كرة القدم', 'achievers' => [$first->slug],
        ]);

        $this->assertSame('gulf-cup-2025', $updated['achievement']['slug']);
        $this->assertNull($updated['achievement']['title_en']);
        $this->assertSame('كأس الخليج', $updated['achievement']['title_ar'], 'fields not passed are kept');
        $this->assertSame('culture', $updated['achievement']['main_type']['slug']);
        $this->assertSame('كرة القدم', $updated['achievement']['discipline_ar']);
        $this->assertTrue($updated['achievement']['is_published']);
        $this->assertSame([$first->slug], $updated['achievement']['achievers']);
        $this->assertSame([$organization->slug], $updated['achievement']['organizations']);

        $this->assertSame(['deleted' => true, 'slug' => 'gulf-cup-2025'], $this->content(DeleteAchievement::class, ['slug' => 'gulf-cup-2025']));
        $this->assertSoftDeleted($achievement);
        $this->tool(GetAchievement::class, ['identifier' => 'gulf-cup-2025'])->assertHasErrors(['No achievement matches']);
    }

    public function test_an_achiever_is_created_searched_read_updated_and_soft_deleted(): void
    {
        Field::factory()->create(['slug' => 'literature']);
        $achievement = Achievement::factory()->create();

        $created = $this->content(CreateAchiever::class, [
            'name_ar' => 'جوخة الحارثي', 'name_en' => 'Jokha Alharthi', 'gender' => 'f', 'main_type' => 'literature',
            'headline_ar' => 'الرواية', 'description_ar' => 'نبذة', 'story_ar' => 'قصتي', 'is_featured' => true, 'in_home_carousel' => true,
            'instagram_url' => 'https://instagram.com/jokha', 'whatsapp_number' => '+968 9999 0000', 'email' => 'j@example.com',
            'achievements' => [$achievement->slug],
        ]);

        $achiever = $created['achiever'];
        $this->assertSame('jokha-alharthi', $achiever['slug']);
        $this->assertSame('f', $achiever['gender']);
        $this->assertTrue($achiever['is_featured']);
        $this->assertTrue($achiever['in_home_carousel']);
        $this->assertFalse($achiever['is_published']);
        $this->assertSame('نبذة', $achiever['description_ar']);
        $this->assertSame('https://instagram.com/jokha', $achiever['instagram_url']);
        $this->assertSame('+968 9999 0000', $achiever['whatsapp_number']);
        $this->assertNull($achiever['email']);
        $this->assertStringContainsString('email', $created['notes'][0]);
        $this->assertSame([$achievement->slug], $achiever['achievements']);
        $this->assertSame(['image', 'tile', 'cutout', 'banner'], array_keys($achiever['images']));

        $model = Achiever::query()->where('slug', 'jokha-alharthi')->sole();
        $this->assertTrue($model->show_in_home_strip);
        $this->assertSame('https://wa.me/96899990000', $model->socialLinks()->where('platform', 'whatsapp')->value('url'));
        $this->assertSame([$model->slug], $achievement->achievers()->pluck('slug')->all());

        $search = $this->content(SearchAchievers::class, ['q' => 'جوخه']);
        $this->assertSame('jokha-alharthi', $search['achievers'][0]['slug']);
        $this->assertSame('literature', $search['achievers'][0]['main_type']);

        $updated = $this->content(UpdateAchiever::class, ['slug' => 'jokha-alharthi', 'in_home_carousel' => false, 'instagram_url' => null, 'achievements' => []]);
        $this->assertFalse($updated['achiever']['in_home_carousel']);
        $this->assertNull($updated['achiever']['instagram_url']);
        $this->assertSame([], $updated['achiever']['achievements']);
        $this->assertSame('جوخة الحارثي', $this->content(GetAchiever::class, ['identifier' => 'jokha-alharthi'])['achiever']['name_ar']);

        $this->content(DeleteAchiever::class, ['slug' => 'jokha-alharthi']);
        $this->assertSoftDeleted($model);
        $this->assertSame(0, $this->content(SearchAchievers::class, ['q' => 'جوخة'])['total']);
    }

    public function test_a_missing_gender_defaults_to_m_with_a_note(): void
    {
        $content = $this->content(CreateAchiever::class, ['name_ar' => 'منجز']);

        $this->assertSame('m', $content['achiever']['gender']);
        $this->assertStringContainsString('gender', $content['notes'][0]);
    }

    public function test_validation_uses_the_old_argument_names(): void
    {
        Achievement::factory()->create(['slug' => 'taken']);

        $this->tool(CreateAchievement::class, ['title_en' => 'No Arabic title'])->assertHasErrors(['title ar']);
        $this->tool(CreateAchievement::class, ['title_ar' => 'x', 'slug' => 'taken'])->assertHasErrors();
        $this->tool(CreateAchievement::class, ['title_ar' => 'x', 'main_type' => 'no-such-field'])->assertHasErrors();
        $this->tool(CreateAchievement::class, ['title_ar' => 'x', 'achievers' => ['no-such-achiever']])->assertHasErrors();
    }

    public function test_tools_check_the_shield_permissions(): void
    {
        $user = $this->userWithRole('admin');
        $user->roles()->first()->revokePermissionTo('Delete:Achievement');
        $achievement = Achievement::factory()->create();

        $this->tool(DeleteAchievement::class, ['slug' => $achievement->slug], $user->fresh())->assertHasErrors(['not allowed to delete achievements']);
        $this->assertNotSoftDeleted($achievement);
    }
}
