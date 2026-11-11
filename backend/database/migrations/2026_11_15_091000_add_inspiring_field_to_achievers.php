<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* «ملهمون من عُمان»: the field shown under an inspiring person (الرياضة، الفن، القرآن الكريم ...), also the wall filter. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievers', function (Blueprint $table) {
            $table->jsonb('inspiring_field')->nullable()->after('inspiring_tagline');
        });
    }

    public function down(): void
    {
        Schema::table('achievers', fn (Blueprint $table) => $table->dropColumn('inspiring_field'));
    }
};
