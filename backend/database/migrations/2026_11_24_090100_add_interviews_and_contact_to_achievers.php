<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * «المقابلات واللقاءات المرئية» of an achiever (shown in «المكتبة المرئية» with the achievements' videos), and their
 * private contact details (dashboard only, never on the site). Both are filled by the achiever's profile review.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievers', function (Blueprint $table) {
            $table->jsonb('interviews')->nullable();       // [{title, url}]
            $table->string('contact_phone', 40)->nullable();
            $table->string('contact_email', 190)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('achievers', function (Blueprint $table) {
            $table->dropColumn(['interviews', 'contact_phone', 'contact_email']);
        });
    }
};
