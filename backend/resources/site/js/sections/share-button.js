// Simple share buttons ([data-share], design js/main.js + org-news.html): native share sheet, else copy the link.
import { label } from '../core/labels.js';

document.querySelectorAll('[data-share]').forEach((button) => {
    button.addEventListener('click', async () => {
        const url = location.href.split('#')[0];
        const note = button.parentElement?.querySelector('.ar-copied');
        try {
            if (navigator.share) {
                await navigator.share({ title: document.title, url });
                return;
            }
            await navigator.clipboard.writeText(url);
            if (note) note.textContent = label('link_copied_short');
        } catch (error) {
            if (note && error?.name !== 'AbortError') note.textContent = url;
        }
    });
});
