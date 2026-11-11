<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\AppendImageChunk;
use App\Mcp\Tools\BeginImageUpload;
use App\Mcp\Tools\FinishImageUpload;
use App\Mcp\Tools\GetAchievement;
use App\Mcp\Tools\GetAchiever;
use App\Mcp\Tools\UploadImage;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Organization;
use App\Models\OrganizationLeader;
use Illuminate\Support\Facades\Storage;

/**
 * upload-image and the chunked begin/append/finish trio: old slots into media collections, sha256 verified.
 */
class McpImageUploadTest extends McpTestCase
{
    private Achievement $achievement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->achievement = Achievement::factory()->create(['slug' => 'upload-image-test']);
    }

    private function bytes(): string
    {
        return base64_decode(self::PNG);
    }

    public function test_an_image_with_a_matching_sha256_becomes_the_achievement_cover(): void
    {
        $sha256 = hash('sha256', $this->bytes());

        $content = $this->content(UploadImage::class, [
            'type' => 'achievement', 'slug' => 'upload-image-test', 'field' => 'cover', 'image' => 'data:image/png;base64,'.self::PNG, 'sha256' => $sha256,
        ]);

        $this->assertTrue($content['uploaded']);
        $this->assertSame('cover', $content['field']);
        $this->assertSame(strlen($this->bytes()), $content['size']);
        $this->assertStringEndsWith('.png', $content['path']);

        $media = $this->achievement->getFirstMedia('cover');
        $this->assertNotNull($media);
        $this->assertSame((string) config('media-library.disk_name'), $media->disk);
        Storage::disk($media->disk)->assertExists($media->getPathRelativeToRoot());

        $cover = $this->content(GetAchievement::class, ['identifier' => 'upload-image-test'])['achievement']['images']['cover'];
        $this->assertSame(['sha256' => $sha256, 'bytes' => strlen($this->bytes()), 'width' => 1, 'height' => 1, 'mime' => 'image/png'], array_diff_key($cover, ['url' => 0]));
        $this->assertSame($media->getUrl(), $cover['url']);

        // A second upload replaces the first (single-file collection).
        $this->content(UploadImage::class, ['type' => 'achievement', 'slug' => 'upload-image-test', 'field' => 'cover', 'image' => self::PNG, 'sha256' => $sha256]);
        $this->assertCount(1, $this->achievement->fresh()->getMedia('cover'));
        $this->assertNotSame($media->id, $this->achievement->fresh()->getFirstMedia('cover')->id);
    }

    public function test_a_sha256_mismatch_is_rejected_and_nothing_is_stored(): void
    {
        $this->tool(UploadImage::class, [
            'type' => 'achievement', 'slug' => 'upload-image-test', 'field' => 'cover', 'image' => self::PNG, 'sha256' => str_repeat('0', 64),
        ])->assertHasErrors(['Checksum mismatch']);

        $this->assertNull($this->achievement->getFirstMedia('cover'));
    }

    public function test_invalid_payloads_slots_and_records_are_rejected(): void
    {
        $base = ['type' => 'achievement', 'slug' => 'upload-image-test', 'field' => 'cover'];

        $this->tool(UploadImage::class, [...$base, 'image' => base64_encode('this is definitely not an image')])->assertHasErrors(['Unsupported image type']);
        $this->tool(UploadImage::class, [...$base, 'field' => 'logo', 'image' => self::PNG])->assertHasErrors(['not valid for type [achievement]']);
        $this->tool(UploadImage::class, [...$base, 'slug' => 'missing', 'image' => self::PNG])->assertHasErrors(['No achievement matches']);
        $this->tool(UploadImage::class, [...$base, 'image' => self::PNG], $this->userWithRole(null))->assertHasErrors(['not allowed']);

        $this->assertNull($this->achievement->getFirstMedia('cover'));
    }

    public function test_one_image_goes_to_several_achiever_slots(): void
    {
        $achiever = Achiever::factory()->create();

        $content = $this->content(UploadImage::class, ['type' => 'achiever', 'slug' => $achiever->slug, 'fields' => ['image', 'tile', 'cutout', 'banner'], 'image' => self::PNG]);

        $this->assertSame(['portrait', 'avatar', 'cutout', 'week'], array_column($content['files'], 'collection'));
        foreach (['portrait', 'avatar', 'cutout', 'week'] as $collection) {
            $this->assertNotNull($achiever->fresh()->getFirstMedia($collection), $collection);
        }

        $images = $this->content(GetAchiever::class, ['identifier' => $achiever->slug])['achiever']['images'];
        $this->assertSame(hash('sha256', $this->bytes()), $images['banner']['sha256']);
    }

    public function test_the_organization_manager_image_is_the_leader_photo_or_the_cover(): void
    {
        $organization = Organization::factory()->create();

        $this->content(UploadImage::class, ['type' => 'organization', 'slug' => $organization->slug, 'fields' => ['logo', 'manager_image'], 'image' => self::PNG]);
        $this->assertNotNull($organization->fresh()->getFirstMedia('logo'));
        $this->assertNotNull($organization->fresh()->getFirstMedia('cover'));

        $leader = OrganizationLeader::query()->create(['organization_id' => $organization->id, 'name' => ['ar' => 'الرئيس'], 'sort_order' => 0]);
        $this->content(UploadImage::class, ['type' => 'organization', 'slug' => $organization->slug, 'field' => 'manager_image', 'image' => self::PNG]);
        $this->assertNotNull($leader->fresh()->getFirstMedia('photo'));
    }

    public function test_a_chunked_upload_is_verified_and_attached(): void
    {
        $bytes = $this->bytes();
        $chunks = str_split($bytes, (int) ceil(strlen($bytes) / 3));

        $begin = $this->content(BeginImageUpload::class, [
            'type' => 'achievement', 'slug' => 'upload-image-test', 'fields' => ['cover'],
            'sha256' => hash('sha256', $bytes), 'total_bytes' => strlen($bytes), 'chunk_count' => count($chunks),
        ]);
        $uploadId = $begin['upload_id'];
        $this->assertSame(1800, $begin['expires_in']);

        // Out of order, one bad checksum (nothing stored for it), then re-sent.
        $this->tool(AppendImageChunk::class, ['upload_id' => $uploadId, 'index' => 2, 'data' => base64_encode($chunks[2]), 'chunk_sha256' => str_repeat('a', 64)])
            ->assertHasErrors(['Chunk checksum mismatch']);
        $this->tool(FinishImageUpload::class, ['upload_id' => $uploadId])->assertHasErrors(['Missing chunk(s) [0, 1, 2]']);

        foreach ([2, 0, 1] as $index) {
            $append = $this->content(AppendImageChunk::class, ['upload_id' => $uploadId, 'index' => $index, 'data' => base64_encode($chunks[$index]), 'chunk_sha256' => hash('sha256', $chunks[$index])]);
        }
        $this->assertSame(0, $append['remaining']);

        // The session belongs to the user who began it.
        $this->tool(FinishImageUpload::class, ['upload_id' => $uploadId], $this->userWithRole('super_admin'))->assertHasErrors(['Unknown or expired upload_id']);

        $finish = $this->content(FinishImageUpload::class, ['upload_id' => $uploadId]);
        $this->assertTrue($finish['uploaded']);
        $this->assertSame(hash('sha256', $bytes), $finish['files'][0]['sha256']);
        $this->assertNotNull($this->achievement->fresh()->getFirstMedia('cover'));

        // Finished sessions are gone.
        $this->tool(FinishImageUpload::class, ['upload_id' => $uploadId])->assertHasErrors(['Unknown or expired upload_id']);
    }

    public function test_a_chunked_upload_with_a_wrong_whole_file_sha256_stores_nothing(): void
    {
        $bytes = $this->bytes();
        $uploadId = $this->content(BeginImageUpload::class, [
            'type' => 'achievement', 'slug' => 'upload-image-test', 'fields' => ['cover'],
            'sha256' => str_repeat('b', 64), 'total_bytes' => strlen($bytes), 'chunk_count' => 1,
        ])['upload_id'];

        $this->content(AppendImageChunk::class, ['upload_id' => $uploadId, 'index' => 0, 'data' => self::PNG, 'chunk_sha256' => hash('sha256', $bytes)]);
        $this->tool(FinishImageUpload::class, ['upload_id' => $uploadId])->assertHasErrors(['Checksum mismatch']);

        $this->assertNull($this->achievement->fresh()->getFirstMedia('cover'));
    }
}
