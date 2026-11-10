{{--
    Light / dark / auto theme. Put <x-theme-script /> in <head> BEFORE @vite so the right theme is set before
    the first paint (no flash). Tailwind's dark: variant follows the `dark` class on <html> (see app.css).
    Any element with data-theme-toggle cycles auto -> light -> dark; [data-theme-label] shows the current mode.
--}}
<script>
    (() => {
        const root = document.documentElement;
        const media = matchMedia('(prefers-color-scheme: dark)');
        const labels = { system: 'Auto', light: 'Light', dark: 'Dark' };
        const theme = window.appTheme = {
            get() {
                try { return localStorage.getItem('theme') || 'system'; } catch (e) { return 'system'; }
            },
            apply(mode) {
                root.classList.toggle('dark', mode === 'dark' || (mode === 'system' && media.matches));
                document.querySelectorAll('[data-theme-label]').forEach((el) => { el.textContent = labels[mode]; });
            },
            set(mode) {
                try { mode === 'system' ? localStorage.removeItem('theme') : localStorage.setItem('theme', mode); } catch (e) {}
                this.apply(mode);
            },
            next() {
                this.set({ system: 'light', light: 'dark', dark: 'system' }[this.get()]);
            },
        };
        root.classList.add('js');
        theme.apply(theme.get());
        media.addEventListener('change', () => theme.get() === 'system' && theme.apply('system'));
        document.addEventListener('click', (event) => {
            if (event.target.closest('[data-theme-toggle]')) theme.next();
        });
        document.addEventListener('DOMContentLoaded', () => theme.apply(theme.get()));
    })();
</script>
