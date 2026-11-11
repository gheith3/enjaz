<?php

namespace App\Mcp;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

/**
 * Shared decode / validate / attach pipeline of the MCP image tools (upload-image and the chunked
 * begin/append/finish trio). The old dashboard's image "slots" (columns) are media library collections here;
 * the verified metadata (sha256, width, height) is kept in the media's custom properties.
 */
final class ImageUploader
{
    public const int MAX_BYTES = 10 * 1024 * 1024;

    /**
     * Old slot name => media collection, per record type (docs/mcp.md, "Image slots").
     *
     * @var array<string, array<string, string>>
     */
    public const array FIELDS = [
        'achievement' => [
            'cover' => 'cover',
        ],
        'achiever' => [
            'image' => 'portrait',
            'tile' => 'avatar',
            'cutout' => 'cutout',
            'banner' => 'week',
        ],
        'organization' => [
            'logo' => 'logo',
            'logo_white' => 'logo_white',
            // Photo of the organization's (current, else first) leader; the organization's cover when it has no leader.
            'manager_image' => 'leader_photo',
        ],
    ];

    /** @var array<string, string> */
    public const array PERMISSIONS = [
        'achievement' => 'Update:Achievement',
        'achiever' => 'Update:Achiever',
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

        return ['mime' => $mime, 'extension' => $extension, 'width' => (int) $size[0], 'height' => (int) $size[1]];
    }

    /**
     * The model and media collection an old slot writes to.
     *
     * @return array{0: Model&HasMedia, 1: string}
     */
    public static function target(Model&HasMedia $record, string $type, string $slot): array
    {
        $collection = self::FIELDS[$type][$slot];

        if ($collection === 'leader_photo' && $record instanceof Organization) {
            $leader = $record->leaders()->reorder()->orderByDesc('is_current')->orderBy('sort_order')->first();

            return $leader ? [$leader, 'photo'] : [$record, 'cover'];
        }

        return [$record, $collection];
    }

    /**
     * Store the bytes in every slot (already validated against FIELDS[$type]) and report per-slot success/failure.
     * Every collection is single-file, so the previous image is replaced.
     *
     * @param  list<string>  $slots
     * @param  array{mime: string, extension: string, width: int, height: int}  $meta
     * @return array{files: list<array<string, mixed>>, failed: array<string, string>}
     */
    public static function attach(Model&HasMedia $record, string $type, array $slots, string $bytes, array $meta): array
    {
        $sha256 = hash('sha256', $bytes);
        $files = [];
        $failed = [];

        foreach ($slots as $slot) {
            [$model, $collection] = self::target($record, $type, $slot);

            try {
                $media = $model->addMediaFromString($bytes)
                    ->usingName($slot)
                    ->usingFileName(Str::uuid().'.'.$meta['extension'])
                    ->withCustomProperties(['sha256' => $sha256, 'width' => $meta['width'], 'height' => $meta['height'], 'source' => 'mcp'])
                    ->toMediaCollection($collection, (string) config('media-library.disk_name'));

                $files[] = [
                    'field' => $slot,
                    'collection' => $collection,
                    'path' => $media->getPathRelativeToRoot(),
                    'url' => $media->getUrl(),
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

        // Saving the record clears the public page cache (ClearsResponseCache), so the new image shows at once.
        if ($files !== []) {
            $record->touch();
        }

        return ['files' => $files, 'failed' => $failed];
    }

    /**
     * Success payload shared by every upload path.
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
     * URL + metadata of the image in a slot. Metadata of images uploaded in the dashboard (no sha256 yet) is computed
     * once from the disk and saved; when the file cannot be read those fields come back null (the URL is kept).
     *
     * @return array{url: string, sha256: ?string, bytes: ?int, width: ?int, height: ?int, mime: ?string}|null
     */
    public static function describe(Model&HasMedia $record, string $type, string $slot): ?array
    {
        [$model, $collection] = self::target($record, $type, $slot);
        $media = $model->getFirstMedia($collection);

        if (! $media instanceof Media) {
            return null;
        }

        if (! $media->hasCustomProperty('sha256')) {
            self::backfill($media);
        }

        return [
            'url' => $media->getUrl(),
            'sha256' => $media->getCustomProperty('sha256'),
            'bytes' => $media->size,
            'width' => $media->getCustomProperty('width'),
            'height' => $media->getCustomProperty('height'),
            'mime' => $media->mime_type,
        ];
    }

    private static function backfill(Media $media): void
    {
        try {
            $stream = $media->stream();
            $bytes = is_resource($stream) ? stream_get_contents($stream) : false;
        } catch (Throwable) {
            $bytes = false;
        }

        if (! is_string($bytes) || $bytes === '') {
            return;
        }

        $size = @getimagesizefromstring($bytes);
        $media->setCustomProperty('sha256', hash('sha256', $bytes))
            ->setCustomProperty('width', is_array($size) ? (int) $size[0] : null)
            ->setCustomProperty('height', is_array($size) ? (int) $size[1] : null)
            ->save();
    }

    /** @return list<string> */
    public static function slots(string $type): array
    {
        return array_keys(self::FIELDS[$type]);
    }
}
