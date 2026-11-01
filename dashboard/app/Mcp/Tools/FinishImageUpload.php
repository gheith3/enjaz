<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\ImageUploader;
use App\Mcp\ImageUploadSession;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('finish-image-upload')]
#[Description('Complete a chunked image upload: verifies every chunk is present, the total size and the whole-file sha256 declared in begin-image-upload, validates the assembled image, then attaches it to every requested slot exactly like upload-image and returns the same payload. On failure the session is kept so you can re-send a bad chunk and finish again.')]
class FinishImageUpload extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string'],
        ]);

        $userId = $this->user()->getAuthIdentifier();
        $session = ImageUploadSession::get($userId, $validated['upload_id']);

        if ($session === null) {
            return Response::error("Unknown or expired upload_id [{$validated['upload_id']}]; call begin-image-upload to start over.");
        }

        $missing = [];

        for ($index = 0; $index < $session['chunk_count']; $index++) {
            if (! array_key_exists($index, $session['chunks'])) {
                $missing[] = $index;
            }
        }

        if ($missing !== []) {
            return Response::error('Missing chunk(s) ['.implode(', ', $missing).']; send them with append-image-chunk, then finish again.');
        }

        $bytes = '';

        for ($index = 0; $index < $session['chunk_count']; $index++) {
            $bytes .= $session['chunks'][$index];
        }

        if (strlen($bytes) !== $session['total_bytes']) {
            return Response::error('Size mismatch: '.json_encode([
                'expected_bytes' => $session['total_bytes'],
                'actual_bytes' => strlen($bytes),
            ]));
        }

        $actual = hash('sha256', $bytes);

        if (! hash_equals($session['sha256'], $actual)) {
            return Response::error('Checksum mismatch: '.json_encode([
                'expected_sha256' => $session['sha256'],
                'actual_sha256' => $actual,
                'decoded_bytes' => strlen($bytes),
            ]));
        }

        $meta = ImageUploader::inspect($bytes);

        if (is_string($meta)) {
            return Response::error($meta);
        }

        $type = $session['type'];

        if (! $this->can(ImageUploader::PERMISSIONS[$type])) {
            return Response::error("You are not allowed to update {$type} records.");
        }

        $record = $this->findImageTarget($type, $session['slug']);

        if ($record === null) {
            return Response::error("No {$type} matches [{$session['slug']}].");
        }

        $result = ImageUploader::attach($record, $type, $session['fields'], $bytes, $meta);

        if ($result['failed'] === []) {
            ImageUploadSession::forget($userId, $validated['upload_id']);
        }

        return $this->structured(ImageUploader::payload($type, $record->slug, $result));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'upload_id' => $schema->string()->required()->description('Upload id returned by begin-image-upload, with every chunk stored via append-image-chunk.'),
        ];
    }
}
