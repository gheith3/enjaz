<?php

/*
 * laravel/mcp settings this app changes (the package defaults apply to every other key). docs/mcp.md.
 */
return [

    // Browser origins allowed to call POST /mcp besides APP_URL and localhost (laravel/mcp rejects any other Origin
    // header). AI connectors call from their servers, usually without an Origin; their web apps are listed in case.
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', (string) env('MCP_ALLOWED_ORIGINS', 'https://claude.ai,https://claude.com'))))),

    // Redirect URIs accepted by dynamic client registration (POST /oauth/register): any, as before.
    'redirect_domains' => ['*'],

];
