<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Enums\FileStoragePath;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Throwable;

#[Name('upload-image')]
#[Description('Attach an image (base64) to a content record: an achievement cover, an achiever photo slot, or an organization logo/manager slot. Fields per type — achievement: cover; achiever: image, tile, cutout, banner; organization: logo, logo_white, manager_image. Replaces any previous file. Max 10 MB, jpeg/png/webp/gif.')]
class UploadImage extends InjazatTool
{
    private const int MAX_BYTES = 10 * 1024 * 1024;

    /** @var array<string, array<string, array{column: string, dir: FileStoragePath}>> */
    private const array FIELDS = [
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
    private const array PERMISSIONS = [
        'achievement' => 'Update:Achievement',
        'achiever' => 'Update:PublicFigure',
        'organization' => 'Update:Organization',
    ];

    /** @var array<string, string> */
    private const array MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(self::FIELDS))],
            'slug' => ['required', 'string'],
            'field' => ['required', 'string'],
            'image' => ['required', 'string'],
        ]);

        $type = $validated['type'];
        $fields = self::FIELDS[$type];

        if (! array_key_exists($validated['field'], $fields)) {
            return Response::error(
                "Field [{$validated['field']}] is not valid for type [{$type}]; allowed: ".implode(', ', array_keys($fields)).'.'
            );
        }

        if (! $this->can(self::PERMISSIONS[$type])) {
            return Response::error("You are not allowed to update {$type} records.");
        }

        $record = match ($type) {
            'achievement' => $this->findAchievement($validated['slug']),
            'achiever' => $this->findAchiever($validated['slug']),
            'organization' => $this->findOrganization($validated['slug']),
        };

        if ($record === null) {
            return Response::error("No {$type} matches [{$validated['slug']}].");
        }

        $base64 = preg_replace('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', '', $validated['image']) ?? '';
        $bytes = base64_decode($base64, true);

        if ($bytes === false || $bytes === '') {
            return Response::error('The image payload is not valid base64.');
        }

        if (strlen($bytes) > self::MAX_BYTES) {
            return Response::error('The image exceeds the 10 MB limit.');
        }

        $mime = finfo_buffer(new \finfo(FILEINFO_MIME_TYPE), $bytes);
        $extension = self::MIME_EXTENSIONS[$mime] ?? null;

        if ($extension === null) {
            return Response::error("Unsupported image type [{$mime}]; allowed: jpeg, png, webp, gif.");
        }

        $field = $fields[$validated['field']];
        $disk = Storage::disk((string) config('filesystems.default'));
        $previous = $record->{$field['column']};
        $path = $field['dir']->value.'/'.Str::uuid().'.'.$extension;

        try {
            $disk->put($path, $bytes, 'public');

            $record->update([$field['column'] => $path]);

            if (is_string($previous) && $previous !== '' && $previous !== $path && $disk->exists($previous)) {
                $disk->delete($previous);
            }
        } catch (Throwable $exception) {
            return Response::error('Failed to store the image: '.$exception->getMessage());
        }

        return $this->structured([
            'uploaded' => true,
            'type' => $type,
            'slug' => $record->slug,
            'field' => $validated['field'],
            'path' => $path,
            'url' => $disk->url($path),
            'size' => strlen($bytes),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(['achievement', 'achiever', 'organization'])->required()->description('Record type the image belongs to.'),
            'slug' => $schema->string()->required()->description('Slug (or UUID id) of the record to attach the image to.'),
            'field' => $schema->string()->required()->description('Image slot: achievement → cover; achiever → image, tile, cutout, banner; organization → logo, logo_white, manager_image.'),
            'image' => $schema->string()->required()->description('Base64-encoded image (a data:image/...;base64, prefix is fine). Max 10 MB decoded; jpeg, png, webp or gif.'),
        ];
    }
}
