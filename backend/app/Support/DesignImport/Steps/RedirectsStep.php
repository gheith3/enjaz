<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Redirect;
use App\Support\DesignImport\DesignMap;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;

/**
 * Old design addresses (/achievers.html ...) to the new pages. Record pages are added by later phases.
 */
class RedirectsStep implements ImportStep
{
    public function key(): string
    {
        return 'redirects';
    }

    public function description(): string
    {
        return 'Redirects from the design\'s *.html addresses';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        foreach (DesignMap::PATHS as $file => $path) {
            $redirect = Redirect::query()->firstOrNew(['from_path' => Redirect::normalize('/'.$file)]);
            if (! $redirect->exists || $force) {
                $redirect->fill(['to_path' => $path, 'status_code' => 301])->save();
            }
        }

        $report->done('redirects: '.count(DesignMap::PATHS).' design pages');
    }
}
