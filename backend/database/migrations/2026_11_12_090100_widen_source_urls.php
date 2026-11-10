<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Source links can be long (percent-encoded Arabic URLs). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sources', fn (Blueprint $table) => $table->text('url')->nullable()->change());
        Schema::table('letters', fn (Blueprint $table) => $table->text('source_url')->nullable()->change());
    }

    public function down(): void
    {
        Schema::table('sources', fn (Blueprint $table) => $table->string('url')->nullable()->change());
        Schema::table('letters', fn (Blueprint $table) => $table->string('source_url')->nullable()->change());
    }
};
