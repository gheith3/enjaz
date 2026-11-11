<?php

namespace Tests\Feature\Import;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Runs the real importer against the repo's design folder (files go to the fake media disk). One class per area so the
 * parallel test runner spreads them over the processes; each imports only the steps it checks.
 */
abstract class ImportTestCase extends TestCase
{
    use RefreshDatabase;

    /** Steps without content records: site settings, menus, pages, redirects. */
    protected const STRUCTURE = ['site', 'menus', 'pages', 'redirects'];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('media-library.disk_name'));
        Queue::fake(); // image conversions are queued jobs: not needed here
    }
}
