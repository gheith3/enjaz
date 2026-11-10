// Public site entry: shared behaviour (header, menu) and the section scripts. Each module looks for its own
// elements and does nothing on pages without them.
import './core/menu.js';
import './core/crumb.js';
import './sections/story.js';
import './sections/achievements-hero.js';
import './sections/achievement-detail.js';
import './sections/profile-filter.js';
// Ported design scripts (resources/site/js/sections/design): achievers page and achiever profile.
import './sections/design/achievers-hero-wall.js';
import './sections/design/achievers-week.js';
import './sections/design/profile-enhance.js';
import './sections/design/profile-medal-stats.js';
import './sections/design/profile-journey.js';
import './sections/design/profile-portrait.js';
import './sections/design/profile-said.js';
import './sections/design/profile-related.js';
import './sections/design/profile-idcard.js';
