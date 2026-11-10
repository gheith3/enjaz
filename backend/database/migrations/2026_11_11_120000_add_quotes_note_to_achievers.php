<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Note under "قالوا عن ..." on the profile (e.g. "translated from English statements"). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievers', fn (Blueprint $table) => $table->jsonb('quotes_note')->nullable()->after('personal_quote_source'));
    }

    public function down(): void
    {
        Schema::table('achievers', fn (Blueprint $table) => $table->dropColumn('quotes_note'));
    }
};
