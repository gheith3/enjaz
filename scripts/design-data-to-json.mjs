#!/usr/bin/env node
// Turns the design's data (window.* objects in design/js, inline JSON blocks, about.html's PAGE object)
// into plain JSON under backend/database/data/design/, the input of `php artisan injazat:import-design`.
// The output is committed, so the PHP import is deterministic and does not need Node.
//
//   node scripts/design-data-to-json.mjs            (from the repo root)
//
// Files are evaluated in a sandbox (node:vm) with only an empty `window`: no DOM, no network, no require.

import { mkdirSync, readFileSync, readdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const design = join(root, 'design');
const out = join(root, 'backend/database/data/design');
mkdirSync(join(out, 'inline'), { recursive: true });

const write = (name, data) => {
    writeFileSync(join(out, name), JSON.stringify(data, null, 2) + '\n');
    console.log(`  ${name}`);
};

// 1. window.* data files. Detail files run after their base file and extend its records in place.
const groups = {
    'universities.json': { files: ['universities-data.js', 'utas-details.js', 'sohar-details.js', 'dhofar-details.js'], global: 'INJAZ_UNIS' },
    'federations.json': { files: ['federations-data.js'], global: 'INJAZ_FEDERATIONS' },
    'award-culture.json': { files: ['award-data.js'], global: 'INJAZ_AWARD' },
    'award-volunteer.json': { files: ['award-volunteer-data.js', 'award-volunteer-media.js'], global: 'INJAZ_VOL' },
    'award-bilarab.json': { files: ['bilarab-data.js'], global: 'INJAZ_BHA' },
    'lab.json': { files: ['lab-data.js'], global: 'LAB' },
};

for (const [name, { files, global }] of Object.entries(groups)) {
    const context = vm.createContext({ window: {} });
    for (const file of files) {
        vm.runInContext(readFileSync(join(design, 'js', file), 'utf8'), context, { filename: file, timeout: 2000 });
    }
    write(name, context.window[global]);
}

// 2. Code that mixes data with DOM work: evaluate only the data declaration.
const extractDeclaration = (source, marker) => {
    const start = source.indexOf(marker);
    if (start < 0) throw new Error(`marker not found: ${marker}`);
    const open = source.indexOf(marker.trim().endsWith('[') ? '[' : '{', start);
    const close = { '[': ']', '{': '}' }[source[open]];
    let depth = 0, inString = null, escaped = false;
    for (let i = open; i < source.length; i++) {
        const ch = source[i];
        if (inString) {
            if (escaped) escaped = false;
            else if (ch === '\\') escaped = true;
            else if (ch === inString) inString = null;
            continue;
        }
        if (ch === '"' || ch === "'" || ch === '`') inString = ch;
        else if (ch === source[open]) depth++;
        else if (ch === close && --depth === 0) return source.slice(open, i + 1);
    }
    throw new Error(`unterminated declaration: ${marker}`);
};
const evaluate = (expression, filename) => vm.runInNewContext(`(${expression})`, {}, { filename, timeout: 2000 });

write('about-roadmap.json', evaluate(extractDeclaration(readFileSync(join(design, 'js/about-roadmap.js'), 'utf8'), 'const M = ['), 'about-roadmap.js'));
write('about-page.json', evaluate(extractDeclaration(readFileSync(join(design, 'about.html'), 'utf8'), 'const PAGE = {'), 'about.html'));
write('org-news.json', evaluate(extractDeclaration(readFileSync(join(design, 'org-news.html'), 'utf8'), 'var N = {'), 'org-news.html'));

// 3. Inline <script type="application/json" id="..."> blocks of every page.
for (const file of readdirSync(design).filter((f) => f.endsWith('.html')).sort()) {
    const html = readFileSync(join(design, file), 'utf8');
    for (const match of html.matchAll(/<script type="application\/json" id="([^"]+)">([\s\S]*?)<\/script>/g)) {
        write(`inline/${file.replace(/\.html$/, '')}--${match[1]}.json`, JSON.parse(match[2]));
    }
}
