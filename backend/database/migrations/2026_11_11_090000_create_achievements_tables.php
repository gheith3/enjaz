<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1: fields (المجالات), scopes (المستوى), achievers, achievements and their children.
 * JSON text columns hold translations {"ar": ..., "en": ...}. Images are media library collections.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('name');
            $table->jsonb('description')->nullable();
            $table->string('icon')->nullable();           // bundled icon (resources/site/assets/svg/cat-*.svg)
            $table->string('card_tone')->default('culture'); // ID card colour: sports | culture
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('scopes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();             // local, gulf, arab, regional, international
            $table->jsonb('name');                         // دولي
            $table->jsonb('achievement_label')->nullable(); // إنجاز دولي
            $table->boolean('is_international')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('achievers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('name');
            $table->string('gender', 1);                  // m | f
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->jsonb('headline')->nullable();         // card subtitle: "الرواية والقصة"
            $table->jsonb('hero_lines')->nullable();       // profile hero, one line per row
            $table->jsonb('short_bio')->nullable();        // ID card
            $table->jsonb('story')->nullable();            // "قصتي"
            $table->jsonb('journey_kicker')->nullable();   // "أبرز محطات"
            $table->jsonb('journey_title')->nullable();    // "رحلتي *في الملاعب*"
            $table->jsonb('personal_quote')->nullable();
            $table->jsonb('personal_quote_context')->nullable();
            $table->jsonb('personal_quote_source')->nullable();
            $table->text('portrait_trace')->nullable();   // SVG path of the gold line around the hero cutout
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_inspiring')->default(false);
            $table->jsonb('inspiring_tagline')->nullable();
            $table->boolean('show_in_home_strip')->default(false);
            $table->date('week_from')->nullable();        // "منجز/منجزة الأسبوع" from this date
            $table->jsonb('week_teaser')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'published_at']);
            $table->index(['gender', 'week_from']);
        });

        Schema::create('achiever_journey_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achiever_id')->constrained()->cascadeOnDelete();
            $table->string('year', 20);
            $table->jsonb('title');
            $table->string('icon')->default('star');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('title');
            $table->jsonb('excerpt')->nullable();
            $table->jsonb('body')->nullable();             // rich text (HTML) per locale
            $table->foreignId('scope_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->jsonb('discipline')->nullable();       // "كرة القدم" (story facts "المجال"; falls back to the field)
            $table->jsonb('profile_tag')->nullable();      // tag on the achiever's profile: "جوائز فردية"
            $table->date('achieved_at')->nullable();
            $table->string('date_precision', 5)->default('day'); // year | month | day
            $table->jsonb('country')->nullable();
            $table->jsonb('highlights')->nullable();       // [{lead: {ar,en}, text: {ar,en}}]
            $table->jsonb('pull_quote')->nullable();
            $table->jsonb('pull_quote_by')->nullable();
            $table->jsonb('pull_quote_role')->nullable();
            $table->jsonb('pull_quote_note')->nullable();
            $table->unsignedTinyInteger('pull_quote_after')->default(2); // after paragraph n
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false); // "لوحة الشرف"
            $table->boolean('show_on_home')->default(false);
            $table->string('home_slot', 10)->nullable();   // feature | card | row
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'published_at']);
            $table->index('achieved_at');
        });

        Schema::create('achievement_achiever', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achiever_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['achievement_id', 'achiever_id']);
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->morphs('sourceable');
            $table->jsonb('title');
            $table->jsonb('subtitle')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->morphs('quotable');
            $table->string('type')->default('person');    // person | official
            $table->jsonb('text');
            $table->jsonb('name');
            $table->jsonb('role')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['quotes', 'sources', 'achievement_achiever', 'achievements', 'achiever_journey_items', 'achievers', 'scopes', 'fields'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
