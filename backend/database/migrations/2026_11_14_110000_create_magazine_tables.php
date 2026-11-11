<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Phase 4c: مجلة إنجازات (articles.html, article.html, writers.html): categories (the tabs), writers, issues and
 * articles. The article text is rich text; the pull quote and the summary box are placed in it by the page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('writers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('name');
            $table->jsonb('role')->nullable();                 // كاتب رأي
            $table->jsonb('bio')->nullable();                  // عن الكاتب
            $table->jsonb('quote')->nullable();                // قلم العدد
            $table->foreignId('article_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('achiever_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_featured')->default(false);   // قلم العدد
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('number')->unique();
            $table->date('issued_at')->nullable();
            $table->jsonb('tagline')->nullable();             // أقلامٌ شابة تكتب عن الأثر ...
            $table->jsonb('quote')->nullable();               // اقتباس العدد
            $table->jsonb('quote_cite')->nullable();
            $table->unsignedBigInteger('lead_article_id')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->jsonb('title');
            $table->jsonb('dek')->nullable();                  // under the title, and the excerpt of the issue's lead
            $table->jsonb('lead')->nullable();                 // first paragraph (bigger)
            $table->jsonb('body')->nullable();                 // rich text
            $table->jsonb('pull_quote')->nullable();
            $table->jsonb('box_title')->nullable();
            $table->jsonb('box_items')->nullable();            // {ar: [..], en: [..]}
            $table->jsonb('cover_caption')->nullable();
            $table->jsonb('tags')->nullable();                 // {ar: [..], en: [..]}
            $table->foreignId('article_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('writer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('read_minutes')->default(1);
            $table->unsignedInteger('views')->default(0);
            $table->boolean('show_on_home')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'published_at']);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreign('lead_article_id')->references('id')->on('articles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('issues', fn (Blueprint $table) => $table->dropForeign(['lead_article_id']));
        Schema::dropIfExists('articles');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('writers');
        Schema::dropIfExists('article_categories');
    }
};
