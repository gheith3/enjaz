<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Phase 5a: the home page. leader_quotes = the hero slider (كلمات القيادة); page_section_items = records picked by
 * hand for a section (spotlight person, sports and culture people), each with its own image and texts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leader_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('variant', 20)->default('sultan');  // design slide layout: sultan | ahd | dy
            $table->jsonb('honorific')->nullable();            // حضرة صاحب الجلالة
            $table->jsonb('name');                              // السلطان هيثم بن طارق المعظم
            $table->jsonb('postnominal')->nullable();          // حفظه الله ورعاه
            $table->jsonb('lead');                              // the bold start of the quote
            $table->jsonb('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('page_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('itemable');                 // achiever, achievement, organization ...
            $table->jsonb('title')->nullable();
            $table->jsonb('label')->nullable();
            $table->jsonb('text')->nullable();
            $table->string('url', 2048)->nullable();
            $table->jsonb('link_label')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Short title on the home cards (أحدث الإنجازات), e.g. «جائزة مان بوكر العالمية».
        Schema::table('achievements', function (Blueprint $table) {
            $table->jsonb('home_title')->nullable()->after('home_slot');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', fn (Blueprint $table) => $table->dropColumn('home_title'));
        Schema::dropIfExists('page_section_items');
        Schema::dropIfExists('leader_quotes');
    }
};
