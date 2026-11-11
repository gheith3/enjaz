<?php

namespace App\Console\Commands;

use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use App\Support\DesignImport\Steps\AchievementsStep;
use App\Support\DesignImport\Steps\AwardsStep;
use App\Support\DesignImport\Steps\ImportStep;
use App\Support\DesignImport\Steps\LabStep;
use App\Support\DesignImport\Steps\MenusStep;
use App\Support\DesignImport\Steps\OrganizationsStep;
use App\Support\DesignImport\Steps\PagesStep;
use App\Support\DesignImport\Steps\RedirectsStep;
use App\Support\DesignImport\Steps\SiteStep;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Fills the database from the static design (repo root /design + backend/database/data/design).
 * Safe to re-run: without --force it only fills what is empty and never overwrites editors' changes.
 * Each content phase adds its step to STEPS.
 */
#[Signature('injazat:import-design {--only=* : Run only these steps (site, menus, pages, redirects, achievements, organizations, awards, lab, news, magazine)} {--force : Overwrite values that already exist} {--path= : Design folder (default: ../design)}')]
#[Description('Import the design\'s content into the database')]
class ImportDesign extends Command
{
    /** @var list<class-string<ImportStep>> */
    private const STEPS = [
        SiteStep::class,
        MenusStep::class,
        PagesStep::class,
        RedirectsStep::class,
        AchievementsStep::class,
        OrganizationsStep::class,
        AwardsStep::class,
        LabStep::class,
    ];

    public function handle(): int
    {
        $design = $this->option('path') ? new DesignSource($this->option('path')) : DesignSource::default();
        if (! $design->exists()) {
            $this->components->error("Design folder not found: {$design->path}");

            return self::FAILURE;
        }

        $only = $this->option('only');
        $report = new ImportReport;

        foreach (self::STEPS as $class) {
            $step = app($class);
            if ($only && ! in_array($step->key(), $only, true)) {
                continue;
            }
            $this->components->task($step->description(), fn () => $step->run($design, $report, (bool) $this->option('force')));
        }

        foreach ($report->lines as $line) {
            $this->line("  <fg=gray>·</> $line");
        }

        if ($report->notes) {
            $this->newLine();
            $this->components->warn('For the editors:');
            $this->components->bulletList($report->notes);
            File::put(storage_path('logs/design-import-notes.log'), now()->toDateTimeString()."\n- ".implode("\n- ", $report->notes)."\n");
        }

        return self::SUCCESS;
    }
}
