<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('achievement_scope_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('achievement_main_type_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('sub_achievement_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->date('achieved_at')->nullable();
            $table->string('country_ar')->nullable();
            $table->string('country_en')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('achieved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
