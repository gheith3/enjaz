<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('body_ar');
            $table->longText('body_en')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('show_on_about')->default(false);
            $table->string('slug')->nullable()->unique();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('published_at');
            $table->index('show_on_about');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
