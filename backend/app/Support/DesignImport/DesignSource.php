<?php

namespace App\Support\DesignImport;

use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Read access to the design folder (repo root /design) and its extracted data
 * (backend/database/data/design, made by scripts/design-data-to-json.mjs).
 */
class DesignSource
{
    /** @var array<string, Crawler> */
    private array $pages = [];

    public function __construct(public readonly string $path) {}

    public static function default(): self
    {
        return new self(base_path('../design'));
    }

    public function exists(): bool
    {
        return File::isDirectory($this->path) && File::exists($this->path.'/index.html');
    }

    public function page(string $file): Crawler
    {
        return $this->pages[$file] ??= new Crawler($this->read($file), 'https://design.local/');
    }

    public function read(string $relative): string
    {
        $full = $this->file($relative);

        return File::get($full);
    }

    public function file(string $relative): string
    {
        $full = $this->path.'/'.ltrim($relative, '/');
        if (! File::exists($full)) {
            throw new RuntimeException("Design file not found: $relative");
        }

        return $full;
    }

    /** @return array<mixed> */
    public function data(string $name): array
    {
        return json_decode(File::get(database_path('data/design/'.$name)), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Visible text of a node with whitespace collapsed.
     */
    public static function text(Crawler $node): string
    {
        return trim(preg_replace('/\s+/u', ' ', $node->text('')));
    }
}
