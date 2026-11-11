<?php

namespace App\Mcp\Tools;

use App\Mcp\ImageUploader;
use App\Mcp\ImageUploadSession;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('begin-image-upload')]
#[Description('Start a chunked image upload for large images (base64 longer than ~8,000 chars): validates the record and every slot, then returns an upload_id. Send the decoded bytes split into chunks with append-image-chunk (recommended ≤ 6,000 base64 chars per chunk, each with its own chunk_sha256), then call finish-image-upload. The session expires after 30 minutes and is bound to your user. Fields per type — achievement: cover; achiever: image, tile, cutout, banner; organization: logo, logo_white, manager_image.')]
class BeginImageUpload extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(ImageUploader::FIELDS))],
            'slug' => ['required', 'string'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['string'],
            'sha256' => ['required', 'string', 'regex:/^[0-9a-f]{64}$/'],
            'total_bytes' => ['required', 'integer', 'min:1', 'max:'.ImageUploader::MAX_BYTES],
            'chunk_count' => ['required', 'integer', 'min:1', 'max:2000'],
        ]);

        $type = $validated['type'];
        $slots = array_values(array_unique($validated['fields']));

        $invalid = array_diff($slots, array_keys(ImageUploader::FIELDS[$type]));

        if ($invalid !== []) {
            return Response::error(
                'Field(s) ['.implode(', ', $invalid)."] not valid for type [{$type}]; allowed: "
                .implode(', ', array_keys(ImageUploader::FIELDS[$type])).'.'
            );
        }

        if (! $this->can(ImageUploader::PERMISSIONS[$type])) {
            return Response::error("You are not allowed to update {$type} records.");
        }

        $record = $this->findImageTarget($type, $validated['slug']);

        if ($record === null) {
            return Response::error("No {$type} matches [{$validated['slug']}].");
        }

        $uploadId = ImageUploadSession::create($this->user()->getAuthIdentifier(), [
            'type' => $type,
            'slug' => $record->slug,
            'fields' => $slots,
            'sha256' => $validated['sha256'],
            'total_bytes' => (int) $validated['total_bytes'],
            'chunk_count' => (int) $validated['chunk_count'],
        ]);

        return $this->structured([
            'upload_id' => $uploadId,
            'type' => $type,
            'slug' => $record->slug,
            'fields' => $slots,
            'chunk_count' => (int) $validated['chunk_count'],
            'expires_in' => ImageUploadSession::TTL_SECONDS,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(['achievement', 'achiever', 'organization'])->required()->description('Record type the image belongs to.'),
            'slug' => $schema->string()->required()->description('Slug (or numeric id) of the record to attach the image to.'),
            'fields' => $schema->array()->required()->description('Image slots the finished image will be attached to: achievement → cover; achiever → image, tile, cutout, banner; organization → logo, logo_white, manager_image.'),
            'sha256' => $schema->string()->required()->description('Hex SHA-256 of the whole decoded file; verified again on finish-image-upload.'),
            'total_bytes' => $schema->integer()->required()->description('Total decoded size in bytes (max 10 MB); verified on finish-image-upload.'),
            'chunk_count' => $schema->integer()->required()->description('Number of chunks you will send (indexes 0..chunk_count-1).'),
        ];
    }
}
