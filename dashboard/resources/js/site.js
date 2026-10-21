// Shared Alpine.js components used across the إنجاز site.
// Registered on Alpine's init event — Alpine itself is provided by Livewire,
// so it must NOT be loaded again here.

// 100vw includes the vertical scrollbar; expose its width so the rem scale fills the visible area exactly.
(function syncScrollbarWidth() {
  const root = document.documentElement;
  const update = () => root.style.setProperty('--sbw', (window.innerWidth - root.clientWidth) + 'px');
  update();
  window.addEventListener('resize', update);
  new ResizeObserver(update).observe(document.body || root);
})();

function siteNav() {
  return {
    mobileOpen: false,
  };
}

function categoryFilter(categories) {
  return {
    active: categories[0] || 'الكل',
    categories: categories,
    isActive(cat) {
      return this.active === cat;
    },
  };
}

function scopeTabs() {
  return {
    active: 'الكل',
    setActive(v) {
      this.active = v;
    },
  };
}

function readMore() {
  return {
    open: false,
  };
}

function carousel(itemCount, perView = 4) {
  return {
    index: 0,
    perView: perView,
    itemCount: itemCount,
    next() {
      const max = Math.max(this.itemCount - this.perView, 0);
      this.index = Math.min(this.index + 1, max);
    },
    prev() {
      this.index = Math.max(this.index - 1, 0);
    },
    get offset() {
      return -(this.index * (100 / this.perView));
    },
  };
}

function photoCarousel(items) {
  return {
    items: items,
    start: 0,
    get visible() {
      const n = this.items.length;
      return Array.from({ length: Math.min(5, n) }, (_, k) => this.items[(this.start + k) % n]);
    },
    next() { this.start = (this.start + 1) % this.items.length; },
    prev() { this.start = (this.start - 1 + this.items.length) % this.items.length; },
  };
}

function contactForm() {
  return {
    name: '',
    email: '',
    message: '',
    submitted: false,
    submit() {
      if (!this.name || !this.email || !this.message) return;
      this.submitted = true;
    },
  };
}

// About-page testimonial cards: shows the previous/current/next quote around index.
function quoteCarousel(items) {
  return {
    index: 0,
    items: items,
    at(o) {
      const n = this.items.length;
      return this.items[(this.index + o + n) % n];
    },
  };
}

// About-page news strip: all items rendered in rotated order starting at index.
function rotatingCarousel(items) {
  return {
    index: 0,
    items: items,
    get visible() {
      return this.items.map((_, k) => this.items[(this.index + k) % this.items.length]);
    },
  };
}

function registerSiteComponents(Alpine) {
  Alpine.data('siteNav', siteNav);
  Alpine.data('categoryFilter', categoryFilter);
  Alpine.data('scopeTabs', scopeTabs);
  Alpine.data('readMore', readMore);
  Alpine.data('carousel', carousel);
  Alpine.data('contactForm', contactForm);
  Alpine.data('photoCarousel', photoCarousel);
  Alpine.data('quoteCarousel', quoteCarousel);
  Alpine.data('rotatingCarousel', rotatingCarousel);
}

// Livewire's Alpine may already have started by the time this module executes
// (script order differs between the Vite dev server and the built assets), so
// register immediately when possible instead of missing the alpine:init event.
if (window.Alpine) {
  registerSiteComponents(window.Alpine);
} else {
  document.addEventListener('alpine:init', () => registerSiteComponents(window.Alpine));
}
