<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\AwardEditions\AwardEditionResource;
use App\Filament\Resources\AwardFeatures\AwardFeatureResource;
use App\Filament\Resources\Awards\AwardResource;
use App\Filament\Resources\AwardWinners\AwardWinnerResource;
use App\Models\Award;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_every_award_screen(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $award = Award::factory()->create();
        $edition = $award->editions()->create(['year' => '2024', 'number' => 1]);
        $winner = $edition->winners()->create(['name' => ['ar' => 'فائز']]);
        $feature = $award->features()->create(['title' => ['ar' => 'قسم']]);

        foreach ([
            AwardResource::getUrl('index'), AwardResource::getUrl('create'), AwardResource::getUrl('edit', ['record' => $award]),
            AwardEditionResource::getUrl('create', ['award' => $award->id]), AwardEditionResource::getUrl('edit', ['record' => $edition]),
            AwardWinnerResource::getUrl('index'), AwardWinnerResource::getUrl('edit', ['record' => $winner]),
            AwardFeatureResource::getUrl('edit', ['record' => $feature]),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
