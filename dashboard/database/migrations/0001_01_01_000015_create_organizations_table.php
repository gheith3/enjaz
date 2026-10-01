<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_type_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_white_path')->nullable();
            $table->string('website_url')->nullable();
            $table->string('manager_name_ar')->nullable();
            $table->string('manager_name_en')->nullable();
            $table->longText('manager_message_ar')->nullable();
            $table->longText('manager_message_en')->nullable();
            $table->string('manager_image_path')->nullable();
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
        Schema::dropIfExists('organizations');
    }
};
