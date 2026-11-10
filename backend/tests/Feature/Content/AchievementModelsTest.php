<?php

namespace Tests\Feature\Content;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Scope;
use App\Support\ArabicDate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dates_are_written_like_the_design(): void
    {
        $date = Carbon::parse('2017-04-29');

        $this->assertSame('٢٩ أبريل ٢٠١٧', ArabicDate::format($date, 'day'));
        $this->assertSame('أبريل ٢٠١٧', ArabicDate::format($date, 'month'));
        $this->assertSame('٢٠١٧', ArabicDate::format($date, 'year'));
    }

    public function test_medal_stats_count_only_published_achievements_by_scope(): void
    {
        $achiever = Achiever::factory()->create();
        $international = Scope::factory()->create();
        $local = Scope::factory()->local()->create();

        $achiever->achievements()->attach(Achievement::factory()->count(2)->create(['scope_id' => $international->id]));
        $achiever->achievements()->attach(Achievement::factory()->create(['scope_id' => $local->id]));
        $achiever->achievements()->attach(Achievement::factory()->draft()->create(['scope_id' => $local->id]));

        $this->assertSame(['total' => 3, 'international' => 2, 'local' => 1], $achiever->medalStats());
    }

    public function test_achiever_of_the_week_is_the_latest_started_week_per_gender(): void
    {
        Achiever::factory()->create(['gender' => 'm', 'week_from' => now()->subWeeks(2)]);
        $current = Achiever::factory()->create(['gender' => 'm', 'week_from' => now()->subDay()]);
        Achiever::factory()->create(['gender' => 'm', 'week_from' => now()->addWeek()]);      // not started yet
        Achiever::factory()->draft()->create(['gender' => 'f', 'week_from' => now()->subDay()]); // draft

        $week = Achiever::ofTheWeek();

        $this->assertTrue($week['m']->is($current));
        $this->assertNull($week['f']);
    }

    public function test_scheduled_records_are_hidden_until_their_time(): void
    {
        Achievement::factory()->create(['published_at' => now()->addDay()]);
        $live = Achievement::factory()->create(['published_at' => now()->subDay()]);
        Achievement::factory()->draft()->create();

        $this->assertSame([$live->id], Achievement::published()->pluck('id')->all());
    }

    public function test_primary_achiever_comes_first(): void
    {
        $achievement = Achievement::factory()->create();
        [$other, $primary] = Achiever::factory()->count(2)->create();
        $achievement->achievers()->attach([$other->id => ['is_primary' => false, 'sort_order' => 1], $primary->id => ['is_primary' => true, 'sort_order' => 2]]);

        $this->assertTrue($achievement->fresh()->primaryAchiever()->is($primary));
    }

    public function test_body_paragraphs_and_read_time(): void
    {
        $achievement = Achievement::factory()->make(['body' => ['ar' => '<p>'.str_repeat('كلمة ', 400).'</p><p>ثانية</p>']]);

        $this->assertCount(2, $achievement->bodyParagraphs());
        $this->assertSame(3, $achievement->readMinutes());
    }
}
