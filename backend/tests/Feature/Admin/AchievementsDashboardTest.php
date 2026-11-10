<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Achievements\AchievementResource;
use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\Achievements\Pages\ListAchievements;
use App\Filament\Resources\Achievers\AchieverResource;
use App\Filament\Resources\Fields\FieldResource;
use App\Filament\Resources\Scopes\ScopeResource;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Scope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchievementsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_every_content_screen(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $field = Field::factory()->create();
        $achiever = Achiever::factory()->create(['field_id' => $field->id]);
        $achievement = Achievement::factory()->create(['field_id' => $field->id, 'scope_id' => Scope::factory()->create()->id]);
        $achievement->achievers()->attach($achiever);

        foreach ([
            AchieverResource::getUrl('index'), AchieverResource::getUrl('create'), AchieverResource::getUrl('edit', ['record' => $achiever]),
            AchievementResource::getUrl('index'), AchievementResource::getUrl('create'), AchievementResource::getUrl('edit', ['record' => $achievement]),
            FieldResource::getUrl('index'), ScopeResource::getUrl('index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_tables_search_translatable_titles(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        Achievement::factory()->create(['title' => ['ar' => 'كأس الخليج']]);
        Achievement::factory()->create(['title' => ['ar' => 'جائزة الأدب']]);

        Livewire::test(ListAchievements::class)
            ->searchTable('الخليج')
            ->assertCanSeeTableRecords(Achievement::query()->where('title->ar', 'كأس الخليج')->get())
            ->assertCountTableRecords(1);
    }

    public function test_an_achievement_is_created_with_its_achievers(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $field = Field::factory()->create();
        $scope = Scope::factory()->create();
        [$primary, $teammate] = Achiever::factory()->count(2)->create();

        Livewire::test(CreateAchievement::class)
            ->fillForm([
                'title' => ['ar' => 'بطولة آسيا', 'en' => 'Asian Championship'],
                'slug' => 'asian-championship',
                'achieverLinks' => [
                    ['achiever_id' => $primary->id, 'is_primary' => true],
                    ['achiever_id' => $teammate->id, 'is_primary' => false],
                ],
                'field_id' => $field->id,
                'scope_id' => $scope->id,
                'achieved_at' => '2024-03-01',
                'date_precision' => 'month',
                'excerpt' => ['ar' => 'ملخص'],
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $achievement = Achievement::query()->where('slug', 'asian-championship')->firstOrFail();
        $this->assertSame('Asian Championship', $achievement->getTranslation('title', 'en'));
        $this->assertTrue($achievement->primaryAchiever()->is($primary));
        $this->assertCount(2, $achievement->achievers);
        $this->assertSame('مارس ٢٠٢٤', $achievement->dateLabel());
    }
}
