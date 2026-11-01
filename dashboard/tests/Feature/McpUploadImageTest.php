<?php

use App\Mcp\Tools\UploadImage;
use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
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
