<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Old URL -> new URL (the design's *.html pages, renamed slugs). Checked only when no route matches.
 */
class Redirect extends Model
{
    use HasFactory, LogsContentActivity;

    protected $fillable = ['from_path', 'to_path', 'status_code'];

    protected function casts(): array
    {
        return ['status_code' => 'integer', 'hits' => 'integer', 'last_hit_at' => 'datetime'];
    }

    public static function normalize(string $path): string
    {
        $parts = parse_url($path);
        $normal = '/'.trim($parts['path'] ?? '', '/');

        return isset($parts['query']) ? $normal.'?'.$parts['query'] : $normal;
    }

    protected function fromPath(): Attribute
    {
        return Attribute::set(fn (string $value) => static::normalize($value));
    }
}
