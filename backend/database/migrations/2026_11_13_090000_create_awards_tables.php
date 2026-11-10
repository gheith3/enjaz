<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3: awards (جوائز). One table for every award; `kind` picks the page design
 * (culture = medals timeline, volunteer = podiums, architecture = cycles with designs).
 * Editions (دورات) hold the winners; categories are branches/prizes/categories/fields;
 * features are extra sections (Bilarab's Expo pavilion and the revival initiative).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kind', 20)->default('culture');  // culture | volunteer | architecture
            $table->jsonb('name');                           // full name
            $table->jsonb('title')->nullable();               // big hero title (جائزة السلطان قابوس)
            $table->jsonb('subtitle')->nullable();            // hero small line (للعمل التطوعي)
            $table->jsonb('eyebrow')->nullable();             // hero eyebrow
            $table->jsonb('motto')->nullable();               // «تطوعي حياة» / motto
            $table->jsonb('date_line')->nullable();           // volunteer: "5 ديسمبر|كل عامين..." (bold|rest)
            $table->string('decree', 40)->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->jsonb('launched')->nullable();            // "15 يناير 2022"
            $table->string('website')->nullable();
            $table->jsonb('about')->nullable();
            $table->jsonb('lead')->nullable();                // intro under a section (architecture idea, culture "how")
            $table->jsonb('vision')->nullable();
            $table->jsonb('honor')->nullable();
            $table->jsonb('firsts')->nullable();
            $table->jsonb('extra')->nullable();               // small per-design labels: {key: {ar, en}}
            $table->jsonb('card_meta')->nullable();           // awards list: "منذ 2011 · ..."
            $table->jsonb('card_text')->nullable();           // awards list: tally line
            $table->string('card_style', 20)->nullable();     // awards list card look: culture | vol
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('award_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10);                       // branch | prize | category | field
            $table->string('key', 30);                        // culture, arts, lit / oman, arab / ind, org
            $table->jsonb('name');
            $table->jsonb('hint')->nullable();
            $table->jsonb('medal')->nullable();               // prize: the medal
            $table->jsonb('basis')->nullable();               // prize: given for one work / whole career
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['award_id', 'kind', 'key']);
        });

        Schema::create('facts', function (Blueprint $table) {
            $table->id();
            $table->morphs('factable');
            $table->string('group', 20);                      // goals | who | path | criteria | facts
            $table->jsonb('label')->nullable();
            $table->jsonb('value')->nullable();
            $table->jsonb('short')->nullable();               // short form (who circles)
            $table->string('icon', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['factable_type', 'factable_id', 'group']);
        });

        Schema::create('award_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number')->nullable();
            $table->string('year', 20);
            $table->string('type', 10)->nullable();           // oman | arab | pause
            $table->jsonb('title')->nullable();
            $table->jsonb('note')->nullable();
            $table->jsonb('status')->nullable();              // current edition: announcement line
            $table->jsonb('place')->nullable();
            $table->string('area', 20)->nullable();
            $table->unsignedInteger('entries_count')->nullable();
            $table->unsignedInteger('finals_count')->nullable();
            $table->jsonb('brief')->nullable();
            $table->jsonb('jury')->nullable();
            $table->jsonb('jury_all')->nullable();
            $table->jsonb('exec')->nullable();
            $table->jsonb('ceremony')->nullable();
            $table->jsonb('withheld')->nullable();            // category keys not awarded this edition
            $table->jsonb('open_fields')->nullable();         // current edition: [{category, text: {ar, en}}]
            $table->jsonb('map_label')->nullable();           // pin on the Oman map (architecture)
            $table->decimal('map_x', 6, 2)->nullable();
            $table->decimal('map_y', 6, 2)->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_partial')->default(false);    // winners only partly documented
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('award_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_edition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('award_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('achiever_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rank')->nullable();  // 1-3, 0 honorary, null = no rank
            $table->jsonb('award_label')->nullable();          // الجائزة التقديرية
            $table->jsonb('name')->nullable();                 // person or organization (or null for a team)
            $table->jsonb('team')->nullable();                 // [{ar, en}] names
            $table->jsonb('work')->nullable();                 // winning work / project
            $table->jsonb('field')->nullable();                // culture: field of the branch
            $table->jsonb('why')->nullable();
            $table->jsonb('place')->nullable();
            $table->string('gender', 1)->nullable();
            $table->boolean('is_group')->default(false);       // group photo (wider crop)
            $table->boolean('is_withheld')->default(false);
            $table->string('source_title')->nullable();
            $table->text('source_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('award_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained()->cascadeOnDelete();
            $table->string('key', 20)->nullable();             // expo | ihya
            $table->jsonb('chip')->nullable();
            $table->jsonb('title');
            $table->jsonb('subtitle')->nullable();             // where
            $table->jsonb('text')->nullable();
            $table->jsonb('note')->nullable();
            $table->jsonb('team')->nullable();
            $table->unsignedInteger('entries_count')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['award_features', 'award_winners', 'award_editions', 'facts', 'award_categories', 'awards'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
