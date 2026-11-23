<?php

namespace App\Console\Commands;

use App\Support\Site\CssBundles;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/** Writes resources/site/css-bundles/*.css from config/site.php (run after changing a page's stylesheet list). */
#[Signature('site:css-bundles')]
#[Description('Generate one stylesheet bundle per page from config/site.php "styles"')]
class BuildCssBundles extends Command
{
    public function handle(): int
    {
        $dir = base_path(CssBundles::DIR);
        File::ensureDirectoryExists($dir);

        $expected = [];
        foreach (CssBundles::keys() as $key) {
            $path = base_path(CssBundles::path($key));
            $expected[] = realpath(dirname($path)).'/'.basename($path);
            File::put($path, CssBundles::contents($key));
        }

        foreach (File::glob($dir.'/*.css') as $file) {
            if (! in_array(realpath($file), $expected, true)) {
                File::delete($file);
                $this->components->warn('Removed '.basename($file));
            }
        }

        $this->components->info(count($expected).' bundles in '.CssBundles::DIR.'. Run npm run build.');

        return self::SUCCESS;
    }
}
