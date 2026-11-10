import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { readdirSync } from 'node:fs';

// Public site: every design stylesheet (resources/site/css) is its own entry, so each page loads exactly
// the design's list in the design's order (config/site.php -> <x-site.layout>). Fonts, icons and the few
// images the CSS references are bundled from resources/site/assets.
const siteCss = readdirSync('resources/site/css')
    .filter((file) => file.endsWith('.css'))
    .map((file) => `resources/site/css/${file}`);

// Icons used by Blade (<img src="{{ Vite::asset(...) }}">) must be in the manifest.
const siteIcons = readdirSync('resources/site/assets/svg')
    .filter((file) => file.endsWith('.svg'))
    .map((file) => `resources/site/assets/svg/${file}`);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/site/js/site.js', 'resources/site/css-site/overrides.css', ...siteCss, ...siteIcons],
            refresh: true,
        }),
    ],
    build: {
        // Keep small icons as files (they are referenced by <img src>), not inlined data URIs.
        assetsInlineLimit: 0,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
