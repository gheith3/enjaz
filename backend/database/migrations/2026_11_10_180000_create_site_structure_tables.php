<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Site structure (phase 0): the fixed pages and their ordered sections, the menus, social links and
 * redirects. Text columns are JSON so they can hold {"ar": ..., "en": ...} (spatie/laravel-translatable).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('title');
            $table->json('meta_description')->nullable();
            $table->json('hero_eyebrow')->nullable();
            $table->json('hero_title')->nullable();
            $table->json('hero_lead')->nullable();
            $table->json('hero_cta_label')->nullable();
            $table->string('hero_cta_url')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['page_id', 'key']);
            $table->index(['page_id', 'is_enabled', 'sort_order']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->json('label');
            $table->json('label_short')->nullable();
            $table->string('url');
            $table->boolean('show_in_desktop_nav')->default(false);
            $table->boolean('opens_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['location', 'is_active', 'sort_order']);
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('linkable');
            $table->string('platform');
            $table->string('url')->nullable();
            $table->string('handle')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};
