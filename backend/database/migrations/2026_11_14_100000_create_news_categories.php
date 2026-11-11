<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Phase 4b: the news section (news.html): categories, and where a news item also appears
 * (همم بلا حدود on inspiring.html, the home page).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('name');
            $table->jsonb('intro')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('news_category_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->boolean('show_in_himam')->default(false)->after('cover_caption');
            $table->boolean('show_on_home')->default(false)->after('show_in_himam');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropConstrainedForeignId('news_category_id');
            $table->dropColumn(['show_in_himam', 'show_on_home']);
        });
        Schema::dropIfExists('news_categories');
    }
};
