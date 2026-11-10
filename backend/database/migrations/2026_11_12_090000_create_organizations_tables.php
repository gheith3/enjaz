<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 2: organizations (ministries, military, private companies, universities and colleges, sports
 * federations) with their children, organization news, and their links to achievements and achievers.
 * One table for every kind; the type picks the page template and which sections show.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();            // gov, military, private, academic, sports
            $table->jsonb('name');                        // حكومي
            $table->jsonb('label');                       // مؤسسة حكومية (card tag)
            $table->jsonb('group_label');                 // المؤسسات الحكومية (list heading)
            $table->string('template')->default('institution'); // institution | university | federation
            $table->string('css_class')->nullable();      // t-gov, t-mil ... (design colours)
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('organization_type_id')->constrained()->restrictOnDelete();
            $table->jsonb('name');
            $table->jsonb('short_name')->nullable();       // كرة القدم, جامعة السلطان قابوس (short)
            $table->string('abbr', 30)->nullable();
            $table->string('academic_group', 10)->nullable(); // uni | college | tech
            $table->string('sector', 10)->nullable();      // gov | private | ahli
            $table->jsonb('city')->nullable();
            $table->jsonb('hq')->nullable();
            $table->jsonb('sport')->nullable();            // federations
            $table->unsignedSmallInteger('founded')->nullable();
            $table->unsignedSmallInteger('declared')->nullable();
            $table->jsonb('formerly')->nullable();
            $table->jsonb('tagline')->nullable();
            $table->jsonb('about')->nullable();
            $table->string('website')->nullable();
            $table->string('color_primary', 9)->nullable();
            $table->string('color_secondary', 9)->nullable();
            $table->decimal('logo_ratio', 5, 2)->nullable(); // logo width / height: < 1.6 = emblem, else plate
            $table->jsonb('focus_areas')->nullable();      // [{ar, en}] chips under the name (ministries)
            $table->jsonb('memberships')->nullable();      // [{name: {ar,en}, year}]
            $table->jsonb('rankings')->nullable();         // [{year, rank}]
            $table->jsonb('ranking_label')->nullable();
            $table->jsonb('ranking_highlights')->nullable(); // [{subject: {ar,en}, text: {ar,en}}]
            $table->jsonb('ranking_note')->nullable();
            $table->jsonb('gallery_intro')->nullable();
            $table->jsonb('gallery_chapters')->nullable(); // [{key, label: {ar,en}}]
            $table->jsonb('gallery_credit')->nullable();   // {text, url}
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_partner')->default(false); // home "شركاء النجاح" marquee
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_type_id', 'is_published']);
        });

        Schema::create('organization_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->jsonb('value');
            $table->jsonb('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('organization_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10);                   // college | unit | center | branch
            $table->jsonb('group_label')->nullable();     // heading of a units group
            $table->unsignedSmallInteger('year')->nullable(); // year the college opened
            $table->jsonb('name');
            $table->jsonb('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('organization_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10)->default('president'); // president | leader | board
            $table->jsonb('name');
            $table->jsonb('title')->nullable();           // honorific: معالي، د.
            $table->jsonb('role')->nullable();
            $table->unsignedSmallInteger('from_year')->nullable();
            $table->unsignedSmallInteger('to_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->jsonb('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('organization_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('youtube_id', 20);
            $table->jsonb('title');
            $table->string('year', 20)->nullable();
            $table->string('duration', 10)->nullable();
            $table->string('era', 20)->nullable();
            $table->jsonb('note')->nullable();
            $table->boolean('is_official')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->morphs('milestoneable');
            $table->string('year', 20);
            $table->jsonb('title');
            $table->jsonb('text')->nullable();
            $table->jsonb('place')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->morphs('letterable');
            $table->jsonb('title')->nullable();           // كلمة رئيس الجامعة
            $table->jsonb('name');
            $table->jsonb('role')->nullable();
            $table->jsonb('body');                        // paragraphs separated by blank lines
            $table->string('source_text')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 12)->default('news');  // news | achievement
            $table->jsonb('tag')->nullable();
            $table->jsonb('title');
            $table->jsonb('dek')->nullable();
            $table->jsonb('summary')->nullable();
            $table->jsonb('body')->nullable();            // rich text
            $table->jsonb('facts')->nullable();           // [{label: {ar,en}, value: {ar,en}}]
            $table->jsonb('cover_caption')->nullable();
            $table->date('happened_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'is_published']);
        });

        Schema::create('achievement_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unique(['achievement_id', 'organization_id']);
        });

        Schema::create('achiever_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achiever_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10)->default('member'); // staff | alum | student | member
            $table->jsonb('role')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['achiever_id', 'organization_id', 'kind']);
        });
    }

    public function down(): void
    {
        foreach (['achiever_organization', 'achievement_organization', 'news', 'letters', 'milestones',
            'organization_videos', 'organization_leaders', 'organization_units', 'organization_stats', 'organizations', 'organization_types'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
