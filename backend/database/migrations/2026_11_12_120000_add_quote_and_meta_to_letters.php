<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A letter can carry a short quote (card teaser) and a meta line (when and where it was written). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->jsonb('quote')->nullable()->after('body');
            $table->jsonb('meta')->nullable()->after('quote');
        });
    }

    public function down(): void
    {
        Schema::table('letters', fn (Blueprint $table) => $table->dropColumn(['quote', 'meta']));
    }
};
