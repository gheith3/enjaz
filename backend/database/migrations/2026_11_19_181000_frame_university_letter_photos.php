<?php

use App\Models\Award;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/*
 * «نافذة المقام»: the five presidents' cutouts reframed alike (same head size, same headroom, head to chest; the UTAS
 * president, standing in the original, from the waist up) so no head is cut by the arch or the keystone. The culture
 * award's board chairman too.
 * Replaces a photo only while it is still the imported original, so a photo changed in /admin is kept.
 */
return new class extends Migration
{
    private const PHOTOS = [
        'squ' => 'squ-6-cut',
        'unizwa' => 'unizwa-rawahi-cut',
        'sohar' => 'sohar-fazari-cut',
        'dhofar' => 'dhofar-rawas-cut',
        'utas' => 'utas-rubaie',
        'culture' => 'chair-khalid',
    ];

    public function up(): void
    {
        foreach (self::PHOTOS as $slug => $original) {
            $letter = $slug === 'culture' ? Award::query()->where('slug', 'culture')->first()?->letter : Organization::query()->where('slug', $slug)->first()?->letter;
            $media = $letter?->getFirstMedia('photo');
            if (! $media || ! str_starts_with($media->file_name, $original)) {
                continue;
            }
            try {
                $letter->addMedia(database_path('data/letters/'.$slug.'-framed.webp'))->preservingOriginal()
                    ->withCustomProperties(['alt' => (string) $letter->name])->toMediaCollection('photo');
            } catch (Throwable $e) {
                Log::warning('University letter photo not reframed', ['slug' => $slug, 'error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        // The originals are replaced in the single-file collection; restore them in /admin if needed.
    }
};
