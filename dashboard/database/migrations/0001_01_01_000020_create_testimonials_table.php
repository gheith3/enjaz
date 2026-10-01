<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('person_name_ar');
            $table->string('person_name_en')->nullable();
            $table->string('position_ar')->nullable();
            $table->string('position_en')->nullable();
            $table->text('quote_ar');
            $table->text('quote_en')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
