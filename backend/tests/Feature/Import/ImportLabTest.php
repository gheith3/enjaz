<?php

namespace Tests\Feature\Import;

use App\Models\LabItem;
use App\Models\Page;

/** Design import: The lab. */
class ImportLabTest extends ImportTestCase
{
    public function test_it_imports_the_lab(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['organizations', 'lab']])->assertSuccessful();

        $this->assertSame(5, LabItem::query()->where('kind', 'invention')->count());
        $this->assertSame(4, LabItem::query()->where('kind', 'publication')->count());
        $sense = LabItem::query()->where('slug', 'senseglass')->firstOrFail();
        $this->assertSame('جامعة صحار', $sense->organization?->name);
        $this->assertSame('ITEX ماليزيا', $sense->event);
        $this->assertSame('2026-05-01', $sense->happened_at->toDateString());
        $this->assertSame(4, $sense->facts()->count());
        $this->assertStringContainsString('<figure>', LabItem::query()->where('slug', 'etienne')->first()->body);
        $this->assertTrue(LabItem::query()->where('slug', 'carbon-ai')->first()->cover_is_illustrative);
        $lab = Page::query()->where('key', 'lab')->first();
        $this->assertSame('62', $lab->label('stat_inventions'));
        $this->assertSame(3, $lab->getMedia('hero')->count());
    }
}
