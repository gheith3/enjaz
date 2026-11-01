<?php

use App\Mcp\Tools\GetAchiever;
use App\Mcp\Tools\UploadImage;
use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\ImageFile;
use App\Models\PublicFigure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

const MCP_TEST_PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

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
        'slug' => 'upload-image-test',
        'title_ar' => 'إنجاز تجريبي',
        'description_ar' => 'وصف تجريبي',
        'achievement_scope_id' => $scope->id,
        'achievement_main_type_id' => $mainType->id,
    ]);

    $this->callTool = fn (array $arguments) => (new UploadImage)->handle(new Request($arguments));
});

test('it uploads a base64 image to an achievement cover', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
    ]);

    expect($result)->toBeInstanceOf(ResponseFactory::class);

    $content = $result->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['slug'])->toBe('upload-image-test')
        ->and($content['field'])->toBe('cover')
        ->and($content['size'])->toBe(strlen(base64_decode(MCP_TEST_PNG)))
        ->and($content['path'])->toStartWith('achievements/covers/')
        ->and($content['path'])->toEndWith('.png');

    Storage::disk($this->disk)->assertExists($content['path']);
    expect($this->achievement->refresh()->cover_image_path)->toBe($content['path']);
});

test('it accepts a data uri prefix and replaces the previous file', function () {
    Storage::disk($this->disk)->put('achievements/covers/old.png', 'old-bytes');
    $this->achievement->update(['cover_image_path' => 'achievements/covers/old.png']);

    $first = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => 'data:image/png;base64,'.MCP_TEST_PNG,
    ]);

    $firstPath = $first->getStructuredContent()['path'];

    Storage::disk($this->disk)->assertMissing('achievements/covers/old.png');
    Storage::disk($this->disk)->assertExists($firstPath);

    $second = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
    ]);

    $secondPath = $second->getStructuredContent()['path'];

    expect($secondPath)->not->toBe($firstPath);
    Storage::disk($this->disk)->assertMissing($firstPath);
    Storage::disk($this->disk)->assertExists($secondPath);
    expect($this->achievement->refresh()->cover_image_path)->toBe($secondPath);
});

test('it rejects a non-image payload', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => base64_encode('this is definitely not an image'),
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue();

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
});

test('it rejects an invalid field for the record type', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'logo',
        'image' => MCP_TEST_PNG,
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue();

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
});

test('it rejects unknown records and users without permission', function () {
    $missing = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'no-such-achievement',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
    ]);

    expect($missing->isError())->toBeTrue();

    Auth::login(User::create([
        'name' => 'No Permission',
        'email' => 'no-permission@example.com',
        'password' => 'secret-password',
    ]));

    $denied = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
    ]);

    expect($denied->isError())->toBeTrue();
    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
});

test('it decodes a data uri prefix with line breaks', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => "data:image/png;base64,\n".implode("\n", str_split(MCP_TEST_PNG, 16))."\n",
    ]);

    expect($result)->toBeInstanceOf(ResponseFactory::class);

    $content = $result->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['size'])->toBe(strlen(base64_decode(MCP_TEST_PNG)));

    Storage::disk($this->disk)->assertExists($content['path']);
});

test('it verifies sha256 and persists image metadata', function () {
    $hash = hash('sha256', base64_decode(MCP_TEST_PNG));

    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
        'sha256' => $hash,
    ]);

    $content = $result->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['files'])->toHaveCount(1)
        ->and($content['files'][0]['field'])->toBe('cover')
        ->and($content['files'][0]['sha256'])->toBe($hash)
        ->and($content['files'][0]['bytes'])->toBe(strlen(base64_decode(MCP_TEST_PNG)))
        ->and($content['files'][0]['width'])->toBe(1)
        ->and($content['files'][0]['height'])->toBe(1)
        ->and($content['files'][0]['mime'])->toBe('image/png')
        ->and($content['files'][0]['url'])->toBeString();

    $meta = ImageFile::query()->find($content['path']);

    expect($meta)->not->toBeNull()
        ->and($meta->sha256)->toBe($hash)
        ->and($meta->bytes)->toBe(strlen(base64_decode(MCP_TEST_PNG)))
        ->and($meta->width)->toBe(1)
        ->and($meta->height)->toBe(1)
        ->and($meta->mime)->toBe('image/png');
});

test('it rejects a wrong sha256 and stores nothing', function () {
    $first = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
    ]);

    $firstPath = $first->getStructuredContent()['path'];

    $wrong = str_repeat('0', 64);
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => MCP_TEST_PNG,
        'sha256' => $wrong,
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue();

    $message = (string) $result->content();

    expect($message)->toContain('expected_sha256')
        ->toContain($wrong)
        ->toContain('actual_sha256')
        ->toContain(hash('sha256', base64_decode(MCP_TEST_PNG)))
        ->toContain('decoded_bytes')
        ->toContain('base64_length');

    expect($this->achievement->refresh()->cover_image_path)->toBe($firstPath);
    Storage::disk($this->disk)->assertExists($firstPath);
    expect(ImageFile::query()->count())->toBe(1);
});

test('it rejects corrupt image bytes and stores nothing', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => base64_encode(hex2bin('89504e470d0a1a0a').random_bytes(64)),
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue();

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
    Storage::disk($this->disk)->assertDirectoryEmpty('achievements/covers');
});

test('it rejects an image over 10 MB', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'field' => 'cover',
        'image' => base64_encode(random_bytes(10 * 1024 * 1024 + 1)),
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue()
        ->and((string) $result->content())->toContain('10 MB');

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
});

test('it rejects fields containing an invalid slot and stores nothing', function () {
    $result = ($this->callTool)([
        'type' => 'achievement',
        'slug' => 'upload-image-test',
        'fields' => ['cover', 'logo'],
        'image' => MCP_TEST_PNG,
    ]);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->isError())->toBeTrue()
        ->and((string) $result->content())->toContain('logo');

    expect($this->achievement->refresh()->cover_image_path)->toBeNull();
    Storage::disk($this->disk)->assertDirectoryEmpty('achievements/covers');
});

test('it attaches one image to several slots via fields', function () {
    Permission::firstOrCreate(['name' => 'Update:PublicFigure', 'guard_name' => 'web']);
    Auth::user()->givePermissionTo('Update:PublicFigure');

    $achiever = PublicFigure::create([
        'slug' => 'upload-image-achiever',
        'name_ar' => 'مُنجِز تجريبي',
        'description_ar' => 'وصف تجريبي',
    ]);

    $result = ($this->callTool)([
        'type' => 'achiever',
        'slug' => 'upload-image-achiever',
        'fields' => ['image', 'tile'],
        'image' => MCP_TEST_PNG,
        'sha256' => hash('sha256', base64_decode(MCP_TEST_PNG)),
    ]);

    $content = $result->getStructuredContent();

    expect($content['uploaded'])->toBeTrue()
        ->and($content['files'])->toHaveCount(2)
        ->and(collect($content['files'])->pluck('field')->all())->toBe(['image', 'tile']);

    $achiever->refresh();

    foreach ($content['files'] as $file) {
        Storage::disk($this->disk)->assertExists($file['path']);
        expect($file['sha256'])->toBe(hash('sha256', base64_decode(MCP_TEST_PNG)));
    }

    expect($achiever->image_path)->toBe($content['files'][0]['path'])
        ->and($achiever->tile_image_path)->toBe($content['files'][1]['path']);
});

test('get-achiever returns slot urls and hashes after upload', function () {
    Permission::firstOrCreate(['name' => 'Update:PublicFigure', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'View:PublicFigure', 'guard_name' => 'web']);
    Auth::user()->givePermissionTo(['Update:PublicFigure', 'View:PublicFigure']);

    PublicFigure::create([
        'slug' => 'upload-image-achiever',
        'name_ar' => 'مُنجِز تجريبي',
        'description_ar' => 'وصف تجريبي',
    ]);

    $hash = hash('sha256', base64_decode(MCP_TEST_PNG));

    ($this->callTool)([
        'type' => 'achiever',
        'slug' => 'upload-image-achiever',
        'field' => 'image',
        'image' => MCP_TEST_PNG,
        'sha256' => $hash,
    ]);

    $result = (new GetAchiever)->handle(new Request(['identifier' => 'upload-image-achiever']));
    $images = $result->getStructuredContent()['achiever']['images'];

    expect($images['image']['url'])->toBeString()
        ->and($images['image']['sha256'])->toBe($hash)
        ->and($images['image']['bytes'])->toBe(strlen(base64_decode(MCP_TEST_PNG)))
        ->and($images['image']['width'])->toBe(1)
        ->and($images['image']['height'])->toBe(1)
        ->and($images['image']['mime'])->toBe('image/png')
        ->and($images['tile'])->toBeNull()
        ->and($images['cutout'])->toBeNull()
        ->and($images['banner'])->toBeNull();
});

test('get-achiever lazily backfills metadata for pre-existing files', function () {
    Permission::firstOrCreate(['name' => 'View:PublicFigure', 'guard_name' => 'web']);
    Auth::user()->givePermissionTo('View:PublicFigure');

    $bytes = base64_decode(MCP_TEST_PNG);
    Storage::disk($this->disk)->put('public-figures/images/legacy.png', $bytes);

    PublicFigure::create([
        'slug' => 'legacy-achiever',
        'name_ar' => 'مُنجِز قديم',
        'description_ar' => 'وصف تجريبي',
        'image_path' => 'public-figures/images/legacy.png',
        'banner_image_path' => 'public-figures/banners/missing.png',
    ]);

    $result = (new GetAchiever)->handle(new Request(['identifier' => 'legacy-achiever']));
    $images = $result->getStructuredContent()['achiever']['images'];

    expect($images['image']['sha256'])->toBe(hash('sha256', $bytes))
        ->and($images['image']['bytes'])->toBe(strlen($bytes))
        ->and($images['image']['width'])->toBe(1)
        ->and($images['image']['height'])->toBe(1)
        ->and($images['image']['mime'])->toBe('image/png')
        ->and(ImageFile::query()->find('public-figures/images/legacy.png'))->not->toBeNull();

    expect($images['banner']['url'])->toBeString()
        ->and($images['banner']['sha256'])->toBeNull()
        ->and($images['banner']['bytes'])->toBeNull()
        ->and($images['banner']['width'])->toBeNull()
        ->and($images['banner']['height'])->toBeNull()
        ->and($images['banner']['mime'])->toBeNull();
});
