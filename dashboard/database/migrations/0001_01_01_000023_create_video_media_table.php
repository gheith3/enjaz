<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('public_figure_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('url');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('slug')->nullable()->unique();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_media');
    }
};
