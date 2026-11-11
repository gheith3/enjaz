<?php

use App\Http\Middleware\EnsureMcpAccess;
use App\Http\Middleware\LogMcpUsage;
use App\Mcp\Servers\InjazatServer;
use Laravel\Mcp\Facades\Mcp;

// The Injazat MCP server (docs/mcp.md): Passport users only, gated by the Use:Mcp permission, every call
// logged, one throttle bucket per user. Same URL, middleware order and tools as the old Enjaz dashboard.
Mcp::web('/mcp', InjazatServer::class)
    ->middleware(['auth:api', EnsureMcpAccess::class, LogMcpUsage::class, 'throttle:mcp']);

// OAuth discovery metadata and dynamic client registration for MCP clients (.well-known/* + POST /oauth/register).
Mcp::oauthRoutes();
