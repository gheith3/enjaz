<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PostgreSQL cannot sort or compare `json`; translatable columns are sorted and searched (dashboard
 * tables, selects), so they are `jsonb`. Phase 0 tables were created as json; convert them.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'pages' => ['title', 'meta_description', 'hero_eyebrow', 'hero_title', 'hero_lead', 'hero_cta_label'],
        'page_sections' => ['settings'],
        'menu_items' => ['label', 'label_short'],
    ];

    public function up(): void
    {
        $this->convert('jsonb');
    }

    public function down(): void
    {
        $this->convert('json');
    }

    private function convert(string $type): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        foreach (self::COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE \"$table\" ALTER COLUMN \"$column\" TYPE $type USING \"$column\"::$type");
                }
            }
        }
    }
};
