<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Filament\Resources\OrganizationTypes\OrganizationTypeResource;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\News;
use App\Models\Organization;
use App\Models\Scope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizationsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_every_organization_screen(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $org = Organization::factory()->create();
        $news = News::factory()->create(['organization_id' => $org->id]);

        foreach ([
            OrganizationResource::getUrl('index'), OrganizationResource::getUrl('create'), OrganizationResource::getUrl('edit', ['record' => $org]),
            NewsResource::getUrl('index'), NewsResource::getUrl('create'), NewsResource::getUrl('edit', ['record' => $news]),
            OrganizationTypeResource::getUrl('index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_an_organization_can_own_an_achievement_without_achievers(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $org = Organization::factory()->create();

        Livewire::test(CreateAchievement::class)
            ->fillForm([
                'title' => ['ar' => 'ذهبية معرض الاختراعات'], 'slug' => 'inventions-gold', 'achieverLinks' => [],
                'organizations' => [$org->id], 'field_id' => Field::factory()->create()->id, 'scope_id' => Scope::factory()->create()->id,
                'achieved_at' => '2023-05-23', 'date_precision' => 'month', 'excerpt' => ['ar' => 'ملخص'],
            ])
            ->call('create')->assertHasNoFormErrors();

        $this->assertTrue(Achievement::query()->where('slug', 'inventions-gold')->first()->organizations->contains($org));
    }

    public function test_an_achievement_needs_an_achiever_or_an_organization(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));

        Livewire::test(CreateAchievement::class)
            ->fillForm([
                'title' => ['ar' => 'بلا صاحب'], 'slug' => 'orphan', 'achieverLinks' => [], 'organizations' => [],
                'field_id' => Field::factory()->create()->id, 'scope_id' => Scope::factory()->create()->id,
                'achieved_at' => '2023-05-23', 'excerpt' => ['ar' => 'ملخص'],
            ])
            ->call('create')->assertHasFormErrors(['organizations']);
    }

    public function test_organization_gathers_its_own_and_its_peoples_achievements(): void
    {
        $org = Organization::factory()->create();
        $own = Achievement::factory()->create(['achieved_at' => '2023-01-01']);
        $own->organizations()->attach($org);
        $staff = Achiever::factory()->create();
        $org->achievers()->attach($staff, ['kind' => 'staff', 'role' => ['ar' => 'أستاذة']]);
        $theirs = Achievement::factory()->create(['achieved_at' => '2024-01-01']);
        $theirs->achievers()->attach($staff);
        Achievement::factory()->draft()->create()->achievers()->attach($staff);

        $rows = $org->allAchievements();

        $this->assertSame([[$theirs->id, 'staff'], [$own->id, 'org']], $rows->map(fn ($r) => [$r['achievement']->id, $r['type']])->all());
        $this->assertSame('أستاذة', $org->achievers()->first()->pivot->roleLabel());
    }
}
