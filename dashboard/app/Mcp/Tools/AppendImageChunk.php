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

#[Name('append-image-chunk')]
#[Description('Send one chunk of a chunked image upload started with begin-image-upload. Chunks may arrive in any order; re-sending the same index replaces it. Recommended ≤ 6,000 base64 chars per chunk. Each chunk is verified against its own chunk_sha256 — on mismatch nothing is stored for that index, fix and re-send it. When every index 0..chunk_count-1 is stored, call finish-image-upload.')]
class AppendImageChunk extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string'],
            'index' => ['required', 'integer', 'min:0'],
            'data' => ['required', 'string'],
            'chunk_sha256' => ['required', 'string', 'regex:/^[0-9a-f]{64}$/'],
        ]);

        $userId = $this->user()->getAuthIdentifier();
        $session = ImageUploadSession::get($userId, $validated['upload_id']);

        if ($session === null) {
            return Response::error("Unknown or expired upload_id [{$validated['upload_id']}]; call begin-image-upload to start over.");
        }

        if ($validated['index'] >= $session['chunk_count']) {
            return Response::error("Chunk index [{$validated['index']}] is out of range; expected 0..".($session['chunk_count'] - 1).'.');
        }

        $decoded = ImageUploader::decode($validated['data']);

        if ($decoded === null) {
            return Response::error('The chunk payload is not valid base64.');
        }

        $actual = hash('sha256', $decoded['bytes']);

        if (! hash_equals($validated['chunk_sha256'], $actual)) {
            return Response::error('Chunk checksum mismatch: '.json_encode([
                'index' => $validated['index'],
                'expected_sha256' => $validated['chunk_sha256'],
                'actual_sha256' => $actual,
            ]));
        }

        $session['chunks'][(int) $validated['index']] = $decoded['bytes'];
        ImageUploadSession::put($userId, $validated['upload_id'], $session);

        $received = count($session['chunks']);

        return $this->structured([
            'upload_id' => $validated['upload_id'],
            'stored' => (int) $validated['index'],
            'received' => $received,
            'remaining' => $session['chunk_count'] - $received,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'upload_id' => $schema->string()->required()->description('Upload id returned by begin-image-upload.'),
            'index' => $schema->integer()->required()->description('Zero-based chunk index (0..chunk_count-1); re-sending an index replaces it.'),
            'data' => $schema->string()->required()->description('Base64-encoded chunk bytes (recommended ≤ 6,000 chars).'),
            'chunk_sha256' => $schema->string()->required()->description('Hex SHA-256 of this chunk’s decoded bytes.'),
        ];
    }
}
