<?php

/*
 * Structure of the public site. Content lives in the database; this file only says which fixed pages
 * exist, which sections each page may contain (the dashboard lets editors order and toggle them), and
 * which stylesheets each page loads, in the design's order (resources/site/css/<name>.css).
 *
 * Section key "<page>.<name>" renders resources/views/site/sections/<page>/<name>.blade.php. A section
 * whose view does not exist yet (later phases) is skipped.
 */

return [
    // First super admin (site:sync assigns the role when nobody has it yet).
    'admin_email' => env('ADMIN_EMAIL'),

    'pages' => [
        'home' => [
            'label' => 'الرئيسية',
            'route' => 'home',
            'sections' => [
                'home.hero' => 'السلايدر: كلمات القيادة',
                'home.achievers' => 'شريط المنجزين',
                'home.stats' => 'الأرقام',
                'home.latest' => 'أحدث الإنجازات',
                'home.spotlight' => 'وجوه من عُمان',
                'home.awards' => 'الجوائز',
                'home.inspiring' => 'ملهمون من عُمان',
                'home.articles' => 'المقالات',
                'home.sports' => 'منجزو الرياضة',
                'home.culture' => 'منجزو الثقافة',
                'home.science' => 'العلوم والابتكار',
                'home.partners' => 'شركاء النجاح',
            ],
        ],
        'achievements' => ['label' => 'الإنجازات', 'route' => 'achievements.index', 'sections' => []],
        'achievers' => ['label' => 'المنجزين', 'route' => 'achievers.index', 'sections' => []],
        'inspiring' => ['label' => 'ملهمون من عُمان', 'sections' => []],
        'awards' => ['label' => 'جوائز السلطان قابوس', 'sections' => []],
        'organizations' => ['label' => 'المؤسسات', 'route' => 'organizations.index', 'features' => ['hero_slides', 'letter'], 'sections' => []],
        'universities' => ['label' => 'الجامعات والكليات', 'route' => 'universities.index', 'sections' => []],
        'lab' => ['label' => 'الاختراعات والابتكارات والمنشورات العلمية', 'sections' => []],
        'news' => ['label' => 'الأخبار', 'sections' => []],
        'magazine' => ['label' => 'المقالات', 'sections' => []],
        'writers' => ['label' => 'أقلام إنجازات', 'sections' => []],
        'about' => ['label' => 'من نحن', 'sections' => []],
        'contact' => ['label' => 'تواصل معنا', 'sections' => []],
    ],

    /*
     * Stylesheets per page, in the design's order. The layout wraps them as:
     * main, site-menu, <before>, desktop, <after>, phone-footer.
     */
    'styles' => [
        'achievements' => [
            'before' => ['achievements-page', 'federations', 'achievements-hero', 'achievements-hero-intro'],
            'after' => ['phone-port-achievements'],
        ],
        'achievers' => [
            'before' => ['achievements-page', 'achievers-page', 'federations', 'achievers-hero'],
            'after' => ['achievers-hero-cards', 'achievers-hero-intro', 'achievers-hero-wall', 'phone-port-achievers'],
        ],
        'achiever' => [
            'before' => ['profile-socials', 'profile-achievements', 'profile-portrait', 'profile-idcard', 'profile-medal-stats', 'profile-enhance', 'profile-journey', 'profile-said', 'profile-hero-map'],
            'after' => [],
        ],
        'organizations' => [
            'before' => ['institutions-page', 'frame-hero'],
            'after' => ['orgs-letter'],
        ],
        'universities' => [
            'before' => ['universities', 'universities-hero'],
            'after' => [],
        ],
        'institution' => [
            'before' => ['home-aisha', 'institutions-page', 'minister-design-5'],
            'after' => [],
        ],
        'university' => [
            'before' => ['home-aisha', 'leader-word', 'minister-design-5', 'universities'],
            'after' => ['university-layout-fixes'],
        ],
        'uni-achievements' => [
            'before' => ['universities'],
            'after' => [],
        ],
        'uni-people' => [
            'before' => ['achievements-page', 'achievers-page', 'universities'],
            'after' => [],
        ],
        'federation' => [
            'before' => ['federation'],
            'after' => [],
        ],
        'org-news' => [
            'before' => ['institutions-page', 'org-news', 'article-page'],
            'after' => [],
        ],
        'achievement' => [
            'before' => ['achievement-detail', 'profile-idcard', 'profile-enhance', 'achievement-enhance'],
            'after' => [],
        ],
        'home' => [
            'before' => ['home-banner', 'home-achievers', 'home-stats-gold', 'home-latest', 'home-aisha', 'home-awards', 'home-inspiring', 'home-articles', 'home-science', 'home-partners', 'home-sports', 'home-upgrade', 'home-hero-cinema'],
            'after' => ['scroll-cue', 'desktop-hero-height', 'home-hero-majlis', 'phone-port-home', 'phone-hero-design', 'phone-arc-motion'],
        ],
    ],

    /*
     * Suggestions for menu links in the dashboard (an editor can also type any URL).
     */
    'links' => [
        '/', '/achievements', '/achievers', '/inspiring', '/awards', '/organizations', '/universities',
        '/lab', '/news', '/magazine', '/writers', '/about', '/contact',
    ],
];
