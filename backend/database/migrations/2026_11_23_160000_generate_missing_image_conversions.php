<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/*
 * تصاميم ٢٠٧٠ — some images were stored without their WebP conversions (card, large), so pages fell back to the
 * original file: the Sultan Qaboos awards banner served a 2.3 MB PNG instead of a ~200 KB WebP. Queue the missing
 * conversions (Horizon makes them); pages switch to the WebP as soon as it exists (Src::of / imageUrl()).
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            Artisan::call('media-library:regenerate', ['--only-missing' => true, '--queue-all' => true, '--force' => true]);
        } catch (\Throwable $e) {
            Log::warning('Missing image conversions were not queued: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        // Derived files only.
    }
};
