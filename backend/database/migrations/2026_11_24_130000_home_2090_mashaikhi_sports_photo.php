<?php

use App\Models\Achiever;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/*
 * «الرياضة» on the home page: Mohammed Al-Mashaikhi's card had no photo (the cut-out PNG was not attached). Attach the
 * same cut-out as a transparent WebP, like the other sports cut-outs.
 */
return new class extends Migration
{
    public function up(): void
    {
        $achiever = Achiever::query()->where('slug', 'mohammed-almashaikhi')->first();
        $section = Page::query()->where('key', 'home')->first()?->sections()->where('key', 'home.sports')->first();
        if (! $achiever || ! $section) {
            return;
        }
        $item = $section->items()->where('itemable_type', $achiever->getMorphClass())->where('itemable_id', $achiever->id)->first();
        $file = database_path('data/home-2090/mashaikhi-cutout.webp');
        if (! $item || $item->hasMedia('image') || ! is_file($file)) {
            return;
        }
        try {
            $item->addMedia($file)->preservingOriginal()->usingFileName('mashaikhi-cutout.webp')->toMediaCollection('image');
        } catch (Throwable $e) {
            Log::error('Home sports: Mashaikhi cut-out not attached: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        // Data only.
    }
};
