<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_figures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('achievement_main_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('headline_ar')->nullable();
            $table->string('headline_en')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en')->nullable();
            $table->longText('story_ar')->nullable();
            $table->longText('story_en')->nullable();
            $table->string('image_path')->nullable();
            $table->string('tile_image_path')->nullable();
            $table->string('cutout_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('in_home_carousel')->default(false);
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('is_featured');
            $table->index('in_home_carousel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_figures');
    }
};
