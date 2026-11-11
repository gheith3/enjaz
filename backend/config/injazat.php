<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MCP (AI assistant) server
    |--------------------------------------------------------------------------
    |
    | The authenticated MCP endpoint (POST /mcp, routes/ai.php, docs/mcp.md):
    | Passport users only, gated by the Use:Mcp permission, every call logged in
    | mcp_usages, one throttle bucket per user. The env names are kept from the
    | old Enjaz dashboard so existing deployments keep working.
    |
    */

    'mcp' => [
        // Master switch: when false, /mcp answers 404 (the endpoint looks absent).
        'enabled' => (bool) env('ENJAZ_MCP_ENABLED', false),

        // IPs and CIDR ranges allowed to call /mcp (comma separated; empty = any).
        'allowed_ips' => array_values(array_filter(
            array_map('trim', explode(',', (string) env('ENJAZ_MCP_ALLOWED_IPS', '')))
        )),

        // Whether personal access tokens (no OAuth) may call /mcp.
        'personal_tokens' => (bool) env('ENJAZ_MCP_PERSONAL_TOKENS', true),
    ],

];
