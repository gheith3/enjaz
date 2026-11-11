<?php

use App\Models\McpUsage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// MCP audit rows older than McpUsage::RETENTION_DAYS (docs/mcp.md).
Schedule::command('model:prune', ['--model' => [McpUsage::class]])->daily();
