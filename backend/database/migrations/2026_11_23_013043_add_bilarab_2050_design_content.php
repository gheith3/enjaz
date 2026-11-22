<?php

use App\Models\Award;
use App\Support\Awards\BilarabContent;
use Illuminate\Database\Migrations\Migration;

/*
 * Content of the approved 2050 design of the Bilarab award page (headline and text of «الفكرة», the goals, the participant
 * card, the «ما بعد الدورتين» lead, the special competition's numbers and the initiative's pillars). Fresh databases get it
 * from the design import; this adds it to the existing award without touching what editors already changed.
 */
return new class extends Migration
{
    public function up(): void
    {
        $award = Award::query()->where('slug', 'bilarab')->first() ?? Award::query()->where('kind', 'architecture')->first();
        if ($award) {
            BilarabContent::apply($award);
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
