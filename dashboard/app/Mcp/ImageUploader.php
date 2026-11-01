<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Enums\FileStoragePath;
use App\Models\ImageFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Shared decode / validate / attach pipeline for the MCP image tools
 * (upload-image and the chunked begin/append/finish trio).
 */
final class ImageUploader
{
    public const int MAX_BYTES = 10 * 1024 * 1024;

    /** @var array<string, array<string, array{column: string, dir: FileStoragePath}>> */
    public const array FIELDS = [
        'achievement' => [
            'cover' => ['column' => 'cover_image_path', 'dir' => FileStoragePath::AchievementCover],
        ],
        'achiever' => [
            'image' => ['column' => 'image_path', 'dir' => FileStoragePath::PublicFigureImage],
            'tile' => ['column' => 'tile_image_path', 'dir' => FileStoragePath::PublicFigureTile],
            'cutout' => ['column' => 'cutout_image_path', 'dir' => FileStoragePath::PublicFigureCutout],
            'banner' => ['column' => 'banner_image_path', 'dir' => FileStoragePath::PublicFigureBanner],
        ],
        'organization' => [
            'logo' => ['column' => 'logo_path', 'dir' => FileStoragePath::OrganizationLogo],
            'logo_white' => ['column' => 'logo_white_path', 'dir' => FileStoragePath::OrganizationLogoWhite],
            'manager_image' => ['column' => 'manager_image_path', 'dir' => FileStoragePath::OrganizationManagerImage],
        ],
    ];

    /** @var array<string, string> */
    public const array PERMISSIONS = [
        'achievement' => 'Update:Achievement',
        'achiever' => 'Update:PublicFigure',
        'organization' => 'Update:Organization',
    ];

    /** @var array<string, string> */
    public const array MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /**
     * Strip an optional data-URL prefix and all whitespace/newlines, then decode strictly.
     *
     * @return array{bytes: string, base64_length: int}|null null when the payload is not valid base64
     */
    public static function decode(string $input): ?array
    {
        $base64 = preg_replace('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', '', trim($input)) ?? '';
        $base64 = preg_replace('/\s+/', '', $base64) ?? '';

        $bytes = base64_decode($base64, true);

        if ($bytes === false || $bytes === '') {
            return null;
        }

        return ['bytes' => $bytes, 'base64_length' => strlen($base64)];
    }

    /**
     * Validate decoded bytes are a real jpeg/png/webp/gif within the size cap and measure them.
     *
     * @return array{mime: string, extension: string, width: int, height: int}|string meta on success, error message on failure
     */
    public static function inspect(string $bytes): array|string
    {
        if (strlen($bytes) > self::MAX_BYTES) {
            return 'The image exceeds the 10 MB limit.';
        }

        $mime = finfo_buffer(new \finfo(FILEINFO_MIME_TYPE), $bytes);
        $extension = self::MIME_EXTENSIONS[$mime] ?? null;

        if ($extension === null) {
            return "Unsupported image type [{$mime}]; allowed: jpeg, png, webp, gif.";
        }

        $size = @getimagesizefromstring($bytes);

        if ($size === false) {
            return 'The payload is not a valid image file.';
        }

        return [
            'mime' => $mime,
            'extension' => $extension,
            'width' => (int) $size[0],
            'height' => (int) $size[1],
        ];
    }

    /**
     * Write the bytes to every slot (already validated against FIELDS[$type]),
     * upsert the image_files metadata and report per-slot success/failure.
     *
     * @param  list<string>  $slots
     * @param  array{mime: string, extension: string, width: int, height: int}  $meta
     * @return array{files: list<array<string, mixed>>, failed: array<string, string>}
     */
    public static function attach(Model $record, string $type, array $slots, string $bytes, array $meta): array
    {
        $disk = Storage::disk((string) config('filesystems.default'));
        $sha256 = hash('sha256', $bytes);
        $files = [];
        $failed = [];

        foreach ($slots as $slot) {
            $field = self::FIELDS[$type][$slot];
            $previous = $record->{$field['column']};
            $path = $field['dir']->value.'/'.Str::uuid().'.'.$meta['extension'];

            try {
                $disk->put($path, $bytes, 'public');

                $record->update([$field['column'] => $path]);

                if (is_string($previous) && $previous !== '' && $previous !== $path && $disk->exists($previous)) {
                    $disk->delete($previous);
                }

                ImageFile::query()->updateOrCreate(
                    ['path' => $path],
                    [
                        'sha256' => $sha256,
                        'bytes' => strlen($bytes),
                        'width' => $meta['width'],
                        'height' => $meta['height'],
                        'mime' => $meta['mime'],
                    ],
                );

                $files[] = [
                    'field' => $slot,
                    'path' => $path,
                    'url' => $disk->url($path),
                    'sha256' => $sha256,
                    'bytes' => strlen($bytes),
                    'width' => $meta['width'],
                    'height' => $meta['height'],
                    'mime' => $meta['mime'],
                ];
            } catch (Throwable $exception) {
                $failed[$slot] = $exception->getMessage();
            }
        }

        return ['files' => $files, 'failed' => $failed];
    }

    /**
     * Success payload shared by every upload path; single-field upload-image
     * calls additionally get the legacy top-level keys.
     *
     * @param  array{files: list<array<string, mixed>>, failed: array<string, string>}  $result
     * @return array<string, mixed>
     */
    public static function payload(string $type, string $slug, array $result): array
    {
        $payload = [
            'uploaded' => $result['failed'] === [],
            'type' => $type,
            'slug' => $slug,
            'files' => $result['files'],
        ];

        if ($result['failed'] !== []) {
            $payload['failed'] = $result['failed'];
        }

        return $payload;
    }

    /**
     * URL + metadata for a stored image path. Metadata for files uploaded before
     * image_files existed is computed lazily from the disk and persisted; when the
     * file cannot be read the metadata fields come back null (the URL is kept).
     *
     * @return array{url: string, sha256: ?string, bytes: ?int, width: ?int, height: ?int, mime: ?string}|null
     */
    public static function describe(?string $path): ?array
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        $disk = Storage::disk((string) config('filesystems.default'));
        $file = ImageFile::query()->find($path);

        if ($file === null) {
            try {
                $bytes = $disk->get($path);
            } catch (Throwable) {
                $bytes = null;
            }

            if (is_string($bytes) && $bytes !== '') {
                $size = @getimagesizefromstring($bytes);

                $file = ImageFile::query()->updateOrCreate(
                    ['path' => $path],
                    [
                        'sha256' => hash('sha256', $bytes),
                        'bytes' => strlen($bytes),
                        'width' => is_array($size) ? (int) $size[0] : 0,
                        'height' => is_array($size) ? (int) $size[1] : 0,
                        'mime' => is_array($size) && isset($size['mime'])
                            ? (string) $size['mime']
                            : (string) (finfo_buffer(new \finfo(FILEINFO_MIME_TYPE), $bytes) ?: ''),
                    ],
                );
            }
        }

        return [
            'url' => $disk->url($path),
            'sha256' => $file?->sha256,
            'bytes' => $file?->bytes,
            'width' => $file?->width,
            'height' => $file?->height,
            'mime' => $file?->mime,
        ];
    }
}
