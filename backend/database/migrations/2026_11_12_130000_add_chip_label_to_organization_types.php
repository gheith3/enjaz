<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Short label of a type on chips and counters (حكومية), next to the card tag (مؤسسة حكومية). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_types', fn (Blueprint $table) => $table->jsonb('chip_label')->nullable()->after('group_label'));
    }

    public function down(): void
    {
        Schema::table('organization_types', fn (Blueprint $table) => $table->dropColumn('chip_label'));
    }
};
