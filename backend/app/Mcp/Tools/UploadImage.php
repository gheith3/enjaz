<?php

namespace App\Mcp\Tools;

use App\Mcp\ImageUploader;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('upload-image')]
#[Description('Attach an image (base64) to a content record: an achievement cover, an achiever photo slot, or an organization logo/manager slot. Fields per type — achievement: cover; achiever: image, tile, cutout, banner; organization: logo, logo_white, manager_image. Pass one slot as `field` or several as `fields` to attach the same image to multiple slots at once. Always pass `sha256` (hex SHA-256 of the decoded bytes) so corruption is caught before anything is written. Replaces any previous file. Max 10 MB decoded, jpeg/png/webp/gif. For payloads longer than ~8,000 base64 chars use begin-image-upload / append-image-chunk / finish-image-upload instead.')]
class UploadImage extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(ImageUploader::FIELDS))],
            'slug' => ['required', 'string'],
            'field' => ['required_without:fields', 'prohibits:fields', 'string'],
            'fields' => ['required_without:field', 'prohibits:field', 'array', 'min:1'],
            'fields.*' => ['string'],
            'image' => ['required', 'string'],
            'sha256' => ['nullable', 'string', 'regex:/^[0-9a-f]{64}$/'],
        ]);

        $type = $validated['type'];
        $slots = isset($validated['fields'])
            ? array_values(array_unique($validated['fields']))
            : [$validated['field']];

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

        $decoded = ImageUploader::decode($validated['image']);

        if ($decoded === null) {
            return Response::error('The image payload is not valid base64.');
        }

        $bytes = $decoded['bytes'];

        if (isset($validated['sha256'])) {
            $actual = hash('sha256', $bytes);

            if (! hash_equals($validated['sha256'], $actual)) {
                return Response::error('Checksum mismatch: '.json_encode([
                    'expected_sha256' => $validated['sha256'],
                    'actual_sha256' => $actual,
                    'decoded_bytes' => strlen($bytes),
                    'base64_length' => $decoded['base64_length'],
                ]));
            }
        }

        $meta = ImageUploader::inspect($bytes);

        if (is_string($meta)) {
            return Response::error($meta);
        }

        $result = ImageUploader::attach($record, $type, $slots, $bytes, $meta);

        $payload = ImageUploader::payload($type, $record->slug, $result);

        if (! isset($validated['fields']) && count($result['files']) === 1) {
            $file = $result['files'][0];
            $payload['field'] = $file['field'];
            $payload['path'] = $file['path'];
            $payload['url'] = $file['url'];
            $payload['size'] = $file['bytes'];
        }

        return $this->structured($payload);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(['achievement', 'achiever', 'organization'])->required()->description('Record type the image belongs to.'),
            'slug' => $schema->string()->required()->description('Slug (or numeric id) of the record to attach the image to.'),
            'field' => $schema->string()->description('Single image slot (use either field or fields): achievement → cover; achiever → image, tile, cutout, banner; organization → logo, logo_white, manager_image.'),
            'fields' => $schema->array()->description('Several image slots to attach the same image to in one call (use either field or fields).'),
            'image' => $schema->string()->required()->description('Base64-encoded image (a data:image/...;base64, prefix and line breaks are fine). Max 10 MB decoded; jpeg, png, webp or gif. Longer than ~8,000 chars? Use the chunked begin-image-upload tools instead.'),
            'sha256' => $schema->string()->description('Hex SHA-256 of the decoded bytes — always send it; the upload is refused on mismatch and nothing is stored.'),
        ];
    }
}
