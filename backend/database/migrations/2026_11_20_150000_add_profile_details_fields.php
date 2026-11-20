<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * An institution's «الرؤية والرسالة وأولويات العمل» (organizations.details, shown in «المؤسسة في سطور»), a milestone's
 * decree as written («مرسوما ١٠ و١١/٢٠٠٢»), a leader's period as written («٢٦ فبراير – ٧ مارس ٢٠١١») with the link of
 * the decree that appointed him, and how a leader's word fits its cut-out portrait in the arch (letters.photo_fit).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->jsonb('details')->nullable();
        });
        Schema::table('milestones', function (Blueprint $table) {
            $table->string('decree', 80)->nullable();
        });
        Schema::table('organization_leaders', function (Blueprint $table) {
            $table->jsonb('period')->nullable();
            $table->string('note_url', 500)->nullable();
        });
        Schema::table('letters', function (Blueprint $table) {
            $table->string('photo_fit', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn('photo_fit');
        });
        Schema::table('organization_leaders', function (Blueprint $table) {
            $table->dropColumn(['period', 'note_url']);
        });
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('decree');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
};
