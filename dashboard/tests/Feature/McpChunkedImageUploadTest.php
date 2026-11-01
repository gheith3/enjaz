<?php

use App\Mcp\Tools\AppendImageChunk;
use App\Mcp\Tools\BeginImageUpload;
use App\Mcp\Tools\FinishImageUpload;
use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

const MCP_CHUNK_PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

beforeEach(function () {
    $this->disk = (string) config('filesystems.default');
    Storage::fake($this->disk);

    Permission::firstOrCreate(['name' => 'Update:Achievement', 'guard_name' => 'web']);

    $user = User::create([
        'name' => 'MCP Tester',
        'email' => 'mcp-tester@example.com',
        'password' => 'secret-password',
    ]);
    $user->givePermissionTo('Update:Achievement');

    Auth::login($user);

    $scope = AchievementScope::create(['title_ar' => 'نطاق تجريبي', 'slug' => 'test-scope']);
    $mainType = AchievementMainType::create(['title_ar' => 'نوع تجريبي', 'slug' => 'test-type']);

    $this->achievement = Achievement::create([
        'slug' => 'chunked-upload-test',
        'title_ar' => 'إنجاز تجريبي',
        'description_ar' => 'وصف تجريبي',
        'achievement_scope_id' => $scope->id,
        'achievement_main_type_id' => $mainType->id,
    ]);

    $this->bytes = base64_decode(MCP_CHUNK_PNG);
    $this->hash = hash('sha256', $this->bytes);
    $this->chunks = array_map('base64_encode', str_split($this->bytes, 30));

    $this->begin = fn (array $overrides = []) => (new BeginImageUpload)->handle(new Request([
        'type' => 'achievement',
        'slug' => 'chunked-upload-test',
        'fields' => ['cover'],
        'sha256' => $this->hash,
        'total_bytes' => strlen($this->bytes),
        'chunk_count' => count($this->chunks),
        ...$overrides,
    ]));

    $this->append = fn (string $uploadId, int $index, ?string $hash = null) => (new AppendImageChunk)->handle(new Request([
        'upload_id' => $uploadId,
        'index' => $index,
        'data' => $this->chunks[$index],
        'chunk_sha256' => $hash ?? hash('sha256', base64_decode($this->chunks[$index])),
    ]));

    $this->finish = fn (string $uploadId) => (new FinishImageUpload)->handle(new Request(['upload_id' => $uploadId]));
});

test('it uploads an image in verified chunks', function () {
    $uploadId = ($this->begin)()->getStructuredContent()['upload_id'];

    foreach ($this->chunks as $index => $chunk) {
        $append = ($this->append)($uploadId, $index);

        expect($append->getStructuredContent()['stored'])->toBe($index)
            ->and($append->getStructuredContent()['received'])->toBe($index + 1);
    }

    $finish = ($this->finish)($uploadId);
    $content = $finish->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['files'])->toHaveCount(1)
        ->and($content['files'][0]['field'])->toBe('cover')
        ->and($content['files'][0]['sha256'])->toBe($this->hash)
        ->and($content['files'][0]['bytes'])->toBe(strlen($this->bytes))
        ->and($content['files'][0]['width'])->toBe(1)
        ->and($content['files'][0]['height'])->toBe(1)
        ->and($content['files'][0]['mime'])->toBe('image/png');

    Storage::disk($this->disk)->assertExists($content['files'][0]['path']);
    expect($this->achievement->refresh()->cover_image_path)->toBe($content['files'][0]['path']);

    // The session is deleted after a successful finish.
    expect(($this->finish)($uploadId)->isError())->toBeTrue();
});

test('it rejects a bad chunk hash and accepts a resent chunk', function () {
    $uploadId = ($this->begin)(['chunk_count' => 1])->getStructuredContent()['upload_id'];

    $bad = (new AppendImageChunk)->handle(new Request([
        'upload_id' => $uploadId,
        'index' => 0,
        'data' => MCP_CHUNK_PNG,
        'chunk_sha256' => str_repeat('0', 64),
    ]));

    expect($bad->isError())->toBeTrue();

    $message = (string) $bad->content();

    expect($message)->toContain('expected_sha256')
        ->toContain(str_repeat('0', 64))
        ->toContain('actual_sha256')
        ->toContain($this->hash);

    $finish = ($this->finish)($uploadId);
    expect($finish->isError())->toBeTrue()
        ->and((string) $finish->content())->toContain('[0]');

    $resent = (new AppendImageChunk)->handle(new Request([
        'upload_id' => $uploadId,
        'index' => 0,
        'data' => MCP_CHUNK_PNG,
        'chunk_sha256' => $this->hash,
    ]));

    expect($resent->getStructuredContent()['received'])->toBe(1);

    $content = ($this->finish)($uploadId)->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['files'][0]['sha256'])->toBe($this->hash);
});

test('it lists the missing chunks on finish', function () {
    $uploadId = ($this->begin)()->getStructuredContent()['upload_id'];

    ($this->append)($uploadId, 0);

    $finish = ($this->finish)($uploadId);

    expect($finish->isError())->toBeTrue();

    $message = (string) $finish->content();

    foreach (range(1, count($this->chunks) - 1) as $missing) {
        expect($message)->toContain((string) $missing);
    }

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
    Storage::disk($this->disk)->assertDirectoryEmpty('achievements/covers');
});

test('it rejects a wrong whole-file hash on finish and stores nothing', function () {
    $uploadId = ($this->begin)(['sha256' => hash('sha256', 'something-else')])->getStructuredContent()['upload_id'];

    foreach ($this->chunks as $index => $chunk) {
        ($this->append)($uploadId, $index);
    }

    $finish = ($this->finish)($uploadId);

    expect($finish->isError())->toBeTrue()
        ->and((string) $finish->content())->toContain('Checksum mismatch')
        ->toContain($this->hash);

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
    Storage::disk($this->disk)->assertDirectoryEmpty('achievements/covers');
});

test('it rejects unknown and expired upload ids', function () {
    $unknown = ($this->finish)((string) Str::uuid());

    expect($unknown->isError())->toBeTrue()
        ->and((string) $unknown->content())->toContain('Unknown or expired');

    $uploadId = ($this->begin)()->getStructuredContent()['upload_id'];

    $this->travel(31)->minutes();

    expect(($this->append)($uploadId, 0)->isError())->toBeTrue();
    expect(($this->finish)($uploadId)->isError())->toBeTrue();
});

test('it rejects an upload id owned by another user', function () {
    $uploadId = ($this->begin)()->getStructuredContent()['upload_id'];

    $other = User::create([
        'name' => 'Other User',
        'email' => 'other@example.com',
        'password' => 'secret-password',
    ]);
    $other->givePermissionTo('Update:Achievement');

    Auth::login($other);

    $append = ($this->append)($uploadId, 0);

    expect($append->isError())->toBeTrue()
        ->and((string) $append->content())->toContain('Unknown or expired');

    expect(($this->finish)($uploadId)->isError())->toBeTrue();
    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
});
