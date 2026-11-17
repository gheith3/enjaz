<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * «إنجازاتي» on the achiever's page (design 9: tiles, timeline and reader): the kind of achievement (medal, award,
 * honor ...), the work it belongs to, the awarding body, a short highlight, a short title and the achiever's
 * top achievement (separate from «لوحة الشرف», which is site wide).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->string('rank', 20)->nullable()->after('profile_tag');
            $table->jsonb('project')->nullable()->after('rank');
            $table->jsonb('issuer')->nullable()->after('project');
            $table->jsonb('stat')->nullable()->after('issuer');
            $table->jsonb('short_title')->nullable()->after('stat');
            $table->boolean('is_profile_highlight')->default(false)->after('short_title');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', fn (Blueprint $table) => $table->dropColumn(['rank', 'project', 'issuer', 'stat', 'short_title', 'is_profile_highlight']));
    }
};
