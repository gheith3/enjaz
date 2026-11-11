<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AppendImageChunk;
use App\Mcp\Tools\BeginImageUpload;
use App\Mcp\Tools\CreateAchievement;
use App\Mcp\Tools\CreateAchiever;
use App\Mcp\Tools\DeleteAchievement;
use App\Mcp\Tools\DeleteAchiever;
use App\Mcp\Tools\FinishImageUpload;
use App\Mcp\Tools\GetAchievement;
use App\Mcp\Tools\GetAchiever;
use App\Mcp\Tools\ListTaxonomies;
use App\Mcp\Tools\SearchAchievements;
use App\Mcp\Tools\SearchAchievers;
use App\Mcp\Tools\UpdateAchievement;
use App\Mcp\Tools\UpdateAchiever;
use App\Mcp\Tools\UploadImage;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

/**
 * The Injazat MCP server (routes/ai.php, docs/mcp.md). Tool names and arguments are those of the old Enjaz dashboard,
 * so connected AI assistants keep working.
 */
#[Name('Injazat')]
#[Version('2.0.0')]
#[Instructions(<<<'TEXT'
You manage Injazat (إنجازات شباب عمان), the Oman youth achievements platform: its achievements and its achievers (public figures). Always call a tool instead of guessing or relying on memory; never invent slugs, ids or taxonomy names.

Content is bilingual: most fields come in Arabic (`*_ar`) and English (`*_en`) variants. Achievements belong to a scope and a main type (the site's fields, المجالات); achievers belong to a main type. There are no sub types: put an achievement's specialty in discipline_ar / discipline_en. Scopes and main types are referenced by slug — call list-taxonomies first to discover valid slugs before creating or updating records. New records are drafts unless is_published is true. When a response carries `notes`, some arguments were adapted or not stored: tell the user.

Achievements link to achievers and organizations by slug arrays; achievers link back to their achievements. Records are never hard-deleted through these tools: delete soft-deletes, and soft-deleted records stay hidden from every tool.

Images are attached with upload-image (base64): achievement → cover; achiever → image, tile, cutout, banner; organization → logo, logo_white, manager_image. Always compute and pass the `sha256` of the decoded bytes — uploads are refused on mismatch before anything is stored. Use the `fields` array to attach one image to several slots in a single call. When the base64 is longer than ~8,000 chars, upload in chunks instead: begin-image-upload, then append-image-chunk (recommended ≤ 6,000 base64 chars per chunk, each with its own chunk_sha256), then finish-image-upload. Base64 via tool arguments is the only upload channel — never try URL fetching or HTTP endpoints. After every upload, confirm with the matching get-* tool by comparing the returned `sha256` of each image slot.

Answer in the user's language, Arabic or English. When a write succeeds, confirm with the record's slug. If a returned list is truncated, say that the answer covers only part of the results.
TEXT)]
class InjazatServer extends Server
{
    public int $defaultPaginationLength = 50;

    protected array $tools = [
        ListTaxonomies::class,
        SearchAchievements::class,
        GetAchievement::class,
        CreateAchievement::class,
        UpdateAchievement::class,
        DeleteAchievement::class,
        SearchAchievers::class,
        GetAchiever::class,
        CreateAchiever::class,
        UpdateAchiever::class,
        DeleteAchiever::class,
        UploadImage::class,
        BeginImageUpload::class,
        AppendImageChunk::class,
        FinishImageUpload::class,
    ];

    /** Tool errors (validation messages included) are in English, as before: the site's locale is Arabic. */
    protected function boot(): void
    {
        parent::boot();

        app()->setLocale('en');
    }
}
