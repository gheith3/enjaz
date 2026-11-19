// «المكتبة المرئية»: مكتبة الفيديو ومكتبة الصور تنبثق كل منهما في نافذة بمربعات صغيرة وعنوان تحت كل مربع.
// مقاطع يوتيوب تعمل داخل النافذة، والصور تفتح في معرض الصور (js/sections/design/profile-enhance.js).
document.querySelectorAll('[data-profile-library]').forEach((library) => {
    library.querySelectorAll('[data-library-dialog]').forEach((button) => {
        const dialog = library.querySelector(`#${button.dataset.libraryDialog}`);
        if (!dialog) return;
        const player = dialog.querySelector('[data-video-player]');
        const stop = () => { if (player) { player.hidden = true; player.replaceChildren(); } };

        button.addEventListener('click', () => {
            if (dialog.showModal) dialog.showModal(); else dialog.setAttribute('open', '');
            document.documentElement.classList.add('library-open');
        });
        dialog.addEventListener('close', () => {
            stop();
            document.documentElement.classList.remove('library-open');
            button.focus();
        });
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog || event.target.closest('[data-library-close]')) { dialog.close(); return; }
            const tile = player && event.target.closest('[data-youtube]');
            if (!tile) return;
            event.preventDefault();
            const frame = document.createElement('iframe');
            frame.src = `https://www.youtube-nocookie.com/embed/${tile.dataset.youtube}?autoplay=1&rel=0`;
            frame.title = tile.getAttribute('title') || '';
            frame.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
            frame.allowFullscreen = true;
            player.replaceChildren(frame);
            player.hidden = false;
            player.scrollIntoView({ block: 'nearest' });
        });
    });
});

// YouTube pictures: the wide HD picture when the video has one, otherwise the small wide one (YouTube answers a
// missing HD picture with a 120×90 placeholder rather than an error).
document.querySelectorAll('[data-profile-library] img[data-fb]').forEach((img) => {
    const swap = () => {
        const fallback = img.dataset.fb;
        if (!fallback) return;
        delete img.dataset.fb;
        img.src = fallback;
    };
    const check = () => { if (img.naturalWidth < 200) swap(); };
    if (img.complete) check();
    else {
        img.addEventListener('load', check, { once: true });
        img.addEventListener('error', swap, { once: true });
    }
});
