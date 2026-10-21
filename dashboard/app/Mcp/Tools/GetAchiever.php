<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get-achiever')]
#[Description('Get one achiever (public figure) by slug or id: the full bilingual record with flags, social fields and linked achievement slugs.')]
#[IsReadOnly]
class GetAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        if (! $this->can('View:PublicFigure')) {
            return Response::error('You are not allowed to view achievers.');
        }

        $achiever = $this->findAchiever((string) $request->get('identifier'));

        if ($achiever === null) {
            return Response::error("No achiever matches [{$request->get('identifier')}].");
        }

        return $this->structured([
            'achiever' => $this->achieverPayload($achiever),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'identifier' => $schema->string()->required()->description('Achiever slug or UUID.'),
        ];
    }
}
