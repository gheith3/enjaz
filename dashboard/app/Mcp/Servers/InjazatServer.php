<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateAchievement;
use App\Mcp\Tools\CreateAchiever;
use App\Mcp\Tools\DeleteAchievement;
use App\Mcp\Tools\DeleteAchiever;
use App\Mcp\Tools\GetAchievement;
use App\Mcp\Tools\GetAchiever;
use App\Mcp\Tools\ListTaxonomies;
use App\Mcp\Tools\SearchAchievements;
use App\Mcp\Tools\SearchAchievers;
use App\Mcp\Tools\UpdateAchievement;
use App\Mcp\Tools\UpdateAchiever;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Injazat')]
#[Version('1.0.0')]
#[Instructions(<<<'TEXT'
You manage Injazat (إنجازات شباب عمان), the Oman youth achievements platform: its achievements and its achievers (public figures). Always call a tool instead of guessing or relying on memory; never invent slugs, ids or taxonomy names.

Content is bilingual: most fields come in Arabic (`*_ar`) and English (`*_en`) variants. Achievements belong to a scope, a main type and optionally a sub type; achievers belong to a main type. All of these are referenced by slug — call list-taxonomies first to discover valid slugs before creating or updating records.

Achievements link to achievers and organizations by slug arrays; achievers link back to their achievements. Records are never hard-deleted through these tools: delete soft-deletes, and soft-deleted records stay hidden from every tool.

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
    ];
}
