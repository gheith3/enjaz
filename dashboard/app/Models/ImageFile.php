<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Verified metadata (hash, size, dimensions, mime) for an image stored on the
 * default disk, keyed by its storage path. Written by the MCP image tools and
 * lazily backfilled for pre-existing files on first read.
 */
class ImageFile extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'path';

    protected $keyType = 'string';

    protected $fillable = [
        'path',
        'sha256',
        'bytes',
        'width',
        'height',
        'mime',
    ];

    protected $casts = [
        'bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];
}
