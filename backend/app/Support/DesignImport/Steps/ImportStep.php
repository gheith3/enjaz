<?php

namespace App\Support\DesignImport\Steps;

use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;

/**
 * One part of the design import. Steps are idempotent: without $force they only fill what is empty, so
 * running the import again never overwrites what editors changed in the dashboard.
 */
interface ImportStep
{
    public function key(): string;

    public function description(): string;

    public function run(DesignSource $design, ImportReport $report, bool $force): void;
}
