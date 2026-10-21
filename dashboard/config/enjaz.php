<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MCP (AI assistant) server
    |--------------------------------------------------------------------------
    |
    | Settings for the authenticated MCP endpoint (POST /mcp, routes/ai.php).
    | Passport users only, gated by the Use:Mcp permission, every call logged
    | in mcp_usages, one throttle bucket per user.
    |
    */

    'mcp' => [
        // Master switch: when false, /mcp answers 404 (the endpoint looks absent).
        'enabled' => (bool) env('ENJAZ_MCP_ENABLED', false),

        // IPs and CIDR ranges allowed to call /mcp (comma separated in the env;
        // empty = any).
        'allowed_ips' => array_values(array_filter(
            array_map('trim', explode(',', (string) env('ENJAZ_MCP_ALLOWED_IPS', '')))
        )),

        // Whether users may mint personal access tokens for MCP clients without OAuth.
        'personal_tokens' => (bool) env('ENJAZ_MCP_PERSONAL_TOKENS', true),
    ],

];
