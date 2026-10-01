<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_organization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('achievement_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['achievement_id', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_organization');
    }
};
