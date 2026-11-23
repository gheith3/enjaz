<?php

use App\Models\Achievement;
use App\Models\Achiever;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/*
 * Photos of the literature achievers added on 23 November 2026 (database/data/literature, uncropped): the profile photo
 * (portrait, card and profile hero) and a cover for each achievement. Only empty slots are filled.
 *
 * «قالوا عنه»: until real quotes are added in the dashboard, each of these achievers without quotes shows passages from
 * their own documented achievements, attributed to the achievement (not to a person).
 */
return new class extends Migration
{
    private const ACHIEVERS = [
        'bushra-khalfan', 'mohsin-alkindi', 'hassan-almatroushi', 'emad-alshanfari', 'zahran-alqasmi',
        'mahmoud-alrahbi', 'saif-alrahbi', 'mohammed-alyahyai', 'jokha-alharthi',
    ];

    public function up(): void
    {
        $dir = database_path('data/literature');
        $manifest = json_decode((string) file_get_contents($dir.'/manifest.json'), true);

        foreach ($manifest['achievers'] as $slug => $file) {
            $achiever = Achiever::query()->where('slug', $slug)->first();
            if (! $achiever) {
                continue;
            }
            foreach (['portrait', 'avatar', 'cutout'] as $collection) {
                $this->attach($achiever, $collection, $dir.'/'.$file);
            }
        }

        foreach ($manifest['achievements'] as $slug => $file) {
            $achievement = Achievement::query()->where('slug', $slug)->first();
            if ($achievement) {
                $this->attach($achievement, 'cover', $dir.'/'.$file);
            }
        }

        foreach (self::ACHIEVERS as $slug) {
            $achiever = Achiever::query()->where('slug', $slug)->first();
            if (! $achiever || $achiever->quotes()->exists()) {
                continue;
            }
            $achiever->achievements()->orderByDesc('achieved_at')->take(3)->get()
                ->each(function (Achievement $achievement, int $i) use ($achiever) {
                    $text = trim((string) $achievement->getTranslation('excerpt', 'ar', false));
                    if ($text === '') {
                        return;
                    }
                    $achiever->quotes()->create([
                        'text' => ['ar' => $text],
                        'name' => ['ar' => $achievement->getTranslation('title', 'ar', false)],
                        'role' => ['ar' => $achievement->achieved_at?->format('Y')],
                        'sort_order' => $i + 1,
                    ]);
                });
        }
    }

    private function attach(Achiever|Achievement $model, string $collection, string $file): void
    {
        if (! is_file($file) || $model->hasMedia($collection)) {
            return;
        }
        try {
            $model->addMedia($file)->preservingOriginal()->toMediaCollection($collection);
        } catch (Throwable $e) {
            Log::warning("Literature photo not attached ({$collection}): ".$e->getMessage());
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
