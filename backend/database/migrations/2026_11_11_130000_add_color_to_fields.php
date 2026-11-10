<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Accent colour of a field's achievement cards (design: --c per field). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fields', fn (Blueprint $table) => $table->string('color', 9)->nullable()->after('icon'));
    }

    public function down(): void
    {
        Schema::table('fields', fn (Blueprint $table) => $table->dropColumn('color'));
    }
};
