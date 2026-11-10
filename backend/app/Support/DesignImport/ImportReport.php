<?php

namespace App\Support\DesignImport;

/**
 * What an import did, plus notes for the editors (values to confirm, placeholders imported as drafts).
 */
class ImportReport
{
    /** @var list<string> */
    public array $lines = [];

    /** @var list<string> */
    public array $notes = [];

    public function done(string $line): void
    {
        $this->lines[] = $line;
    }

    public function note(string $note): void
    {
        $this->notes[] = $note;
    }
}
