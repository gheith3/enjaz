// Achiever profile "إنجازاتي": scope chips (all / international / local), the medal rows, and the "الترتيب"
// panel (year, type). The design's panel (js/main.js f-box) only displayed choices; here it filters.
const grid = document.querySelector('[data-ach-grid]');
if (grid) {
    const cards = [...grid.querySelectorAll('.ach[data-scope]')];
    const empty = document.querySelector('[data-ach-empty]');
    const chips = [...document.querySelectorAll('[data-scope-filter]')];
    const panel = document.querySelector('[data-profile-sort-panel]');
    const toggle = document.querySelector('[data-profile-sort-toggle]');
    const section = document.getElementById('achievements');
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const state = { scope: 'all', year: '', type: '' };

    const apply = () => {
        let shown = 0;
        cards.forEach((card) => {
            const ok = (state.scope === 'all' || card.dataset.scope === state.scope)
                && (!state.year || (card.dataset.date || '').startsWith(state.year))
                && (!state.type || card.dataset.type === state.type);
            card.hidden = !ok;
            if (ok) shown++;
        });
        if (empty) empty.hidden = shown > 0;
        chips.forEach((chip) => {
            const on = chip.dataset.scopeFilter === state.scope;
            chip.classList.toggle('active', on);
            chip.setAttribute('aria-pressed', String(on));
        });
        document.querySelectorAll('.ms-row').forEach((row) => {
            const on = (row.dataset.kind === 'intl' ? 'international' : 'local') === state.scope;
            row.setAttribute('aria-pressed', String(on));
        });
    };

    window.injazProfileFilter = (scope) => { state.scope = scope || 'all'; apply(); };
    chips.forEach((chip) => chip.addEventListener('click', () => window.injazProfileFilter(chip.dataset.scopeFilter)));
    document.querySelectorAll('.ms-row').forEach((row) => row.addEventListener('click', () => {
        window.injazProfileFilter(row.dataset.kind === 'intl' ? 'international' : 'local');
        section?.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
    }));

    if (panel && toggle) {
        const sheet = panel.querySelector('[data-sheet]');
        const boxes = Object.fromEntries([...panel.querySelectorAll('.f-box')].map((b) => [b.dataset.key, b]));
        const options = { year: JSON.parse(panel.dataset.years || '[]'), type: JSON.parse(panel.dataset.types || '[]') };
        const allLabel = panel.dataset.all;
        let openKey = null;

        const paint = () => Object.entries(boxes).forEach(([key, box]) => {
            const current = options[key].find((o) => o.v === state[key]);
            box.querySelector('.f-val').textContent = current ? current.l : allLabel;
            box.classList.toggle('set', Boolean(current));
            box.setAttribute('aria-expanded', String(openKey === key));
        });
        const showSheet = (key) => {
            openKey = openKey === key ? null : key;
            paint();
            sheet.hidden = !openKey;
            if (!openKey) return;
            sheet.innerHTML = '<div class="f-opts">' + options[key].map((o) => `<button type="button" class="f-opt" data-v="${o.v}" aria-pressed="${o.v === state[key]}">${o.l}</button>`).join('') + '</div>';
            sheet.querySelectorAll('.f-opt').forEach((button) => button.addEventListener('click', () => {
                state[key] = state[key] === button.dataset.v ? '' : button.dataset.v;
                openKey = null; sheet.hidden = true; paint(); apply();
            }));
        };
        Object.entries(boxes).forEach(([key, box]) => box.addEventListener('click', () => showSheet(key)));
        toggle.addEventListener('click', () => {
            const open = !panel.classList.contains('open');
            panel.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', String(open));
        });
        panel.querySelector('[data-filter-reset]')?.addEventListener('click', () => {
            state.year = ''; state.type = ''; openKey = null; sheet.hidden = true; paint(); apply();
        });
        paint();
    }
}
