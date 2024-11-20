<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_scope_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_main_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_achievement_type_id')->constrained()->cascadeOnDelete();
            $table->string("title_ar");
            $table->longText("description_ar");
            $table->string("title_en")->nullable();
            $table->longText("description_en")->nullable();
            $table->text('cover_image_path');
            $table->text('video_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
