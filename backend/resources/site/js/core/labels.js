// UI strings for scripts, rendered by the layout from lang/<locale>/site.php ('js').
let cache = null;

export function label(key, replace = {}) {
    if (!cache) {
        try {
            cache = JSON.parse(document.getElementById('site-labels')?.textContent || '{}');
        } catch {
            cache = {};
        }
    }
    let text = cache[key] ?? key;
    for (const [name, value] of Object.entries(replace)) text = text.replace(`:${name}`, value);
    return text;
}

export const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
