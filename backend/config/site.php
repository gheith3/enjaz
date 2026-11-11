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
        'inspiring' => [
            'label' => 'ملهمون من عُمان', 'route' => 'inspiring', 'sections' => ['inspiring.himam' => 'هِمَمٌ بلا حدود'],
            'labels' => [
                'crumb' => 'الاسم في الشريط العلوي', 'discover' => 'رابط «اكتشف قصصهم»', 'wall_title' => 'عنوان جدار الصور', 'cta_title' => 'الترشيح: العنوان', 'cta_text' => 'الترشيح: النص',
                'cta_button' => 'الترشيح: نص الزر', 'cta_url' => 'الترشيح: الرابط (فارغ = تواصل معنا)',
            ],
        ],
        'awards' => ['label' => 'جوائز السلطان قابوس', 'route' => 'awards.index', 'sections' => []],
        'organizations' => ['label' => 'المؤسسات', 'route' => 'organizations.index', 'features' => ['hero_slides', 'letter'], 'sections' => []],
        'universities' => ['label' => 'الجامعات والكليات', 'route' => 'universities.index', 'sections' => []],
        'lab' => [
            'label' => 'الاختراعات والابتكارات والمنشورات العلمية', 'route' => 'lab.index', 'features' => ['cutout', 'hero_slides'], 'sections' => [],
            'labels' => [
                'crumb' => 'الاسم في الشريط العلوي', 'subtitle' => 'السطر العريض تحت العنوان',
                'stat_inventions' => 'الرقم الأول (فارغ = عدد الابتكارات المنشورة)', 'stat_inventions_label' => 'وصف الرقم الأول',
                'stat_science' => 'الرقم الثاني (فارغ = عدد كل عناصر المختبر)', 'stat_science_label' => 'وصف الرقم الثاني',
                'stats_note' => 'مصدر الأرقام (تحت الأرقام)',
                'showcase_tag' => 'عنوان صغير: قاعة العرض', 'showcase_title' => 'عنوان قسم الابتكارات',
                'table_tag' => 'عنوان صغير: جدول المعرفة', 'table_title' => 'عنوان قسم المنشورات',
                'cta_title' => 'الدعوة في آخر الصفحة: العنوان', 'cta_text' => 'الدعوة: النص',
                'inventions_intro' => 'صفحة كل الابتكارات: النص (:count للعدد)', 'publications_intro' => 'صفحة كل المنشورات: النص (:count للعدد)',
            ],
        ],
        'news' => ['label' => 'الأخبار', 'route' => 'news.index', 'sections' => []],
        'magazine' => [
            'label' => 'المقالات', 'route' => 'magazine.index', 'sections' => [],
            'labels' => [
                'note_kicker' => 'بطاقة الهيدر: السطر الصغير', 'note_text' => 'بطاقة الهيدر: النص (| لسطر جديد)',
                'mast_title' => 'اسم المجلة (الجزء الملوّن بعد |)', 'cta_title' => 'الدعوة في آخر الصفحة: العنوان', 'cta_text' => 'الدعوة: النص',
                'cta_url' => 'الدعوة: الرابط (فارغ = تواصل معنا)',
            ],
        ],
        'writers' => [
            'label' => 'أقلام إنجازات', 'route' => 'magazine.writers', 'sections' => [],
            'labels' => [
                'kicker' => 'السطر الصغير فوق العنوان', 'cta_title' => 'الدعوة في آخر الصفحة: العنوان', 'cta_text' => 'الدعوة: النص',
                'cta_url' => 'الدعوة: الرابط (فارغ = تواصل معنا)',
            ],
        ],
        'about' => [
            'label' => 'من نحن', 'route' => 'about', 'fixed_order' => true,
            'sections' => [
                'about.fields' => 'مجالات الإنجاز (الحلقة الدوّارة)', 'about.journey' => 'خارطة العمل', 'about.founder' => 'كلمة المؤسس',
                'about.testimonials' => 'قالوا عن إنجازات', 'about.team' => 'فريق العمل', 'about.achievements' => 'إنجازاتنا',
                'about.media' => 'المكتبة الإعلامية',
            ],
            'labels' => [
                'crumb' => 'الاسم في الشريط العلوي', 'nav_top' => 'شريط الأقسام: البداية', 'nav_mission' => 'شريط الأقسام: الرسالة',
                'fact1_n' => 'الرقم ١', 'fact1_t' => 'وصف الرقم ١', 'fact2_n' => 'الرقم ٢', 'fact2_t' => 'وصف الرقم ٢', 'fact3_n' => 'الرقم ٣', 'fact3_t' => 'وصف الرقم ٣',
                'journey_button' => 'زر «رحلتنا»', 'contact_button' => 'زر «تواصل معنا»',
                'mission' => 'الرسالة: النص', 'vision' => 'الرؤية: النص',
                'value1' => 'القيمة ١: النص', 'value1_tag' => 'القيمة ١: الكلمة', 'value2' => 'القيمة ٢: النص', 'value2_tag' => 'القيمة ٢: الكلمة',
                'value3' => 'القيمة ٣: النص', 'value3_tag' => 'القيمة ٣: الكلمة', 'value4' => 'القيمة ٤: النص', 'value4_tag' => 'القيمة ٤: الكلمة',
            ],
        ],
        'contact' => [
            'label' => 'تواصل معنا', 'route' => 'contact', 'features' => ['cutout'], 'sections' => [],
            'labels' => [
                'email' => 'البريد في الصفحة (وتصل إليه رسائل النموذج؛ فارغ = بريد الموقع)', 'phones' => 'أرقام الهاتف (مفصولة بفاصلة؛ فارغ = أرقام الموقع)',
                'handle' => 'اسم الحساب في التواصل الاجتماعي (@injazat_oman)', 'team_name' => 'اسم الفريق في المحادثة',
                'chat1' => 'المحادثة: الرسالة ١', 'chat2' => 'المحادثة: الرسالة ٢', 'chat3' => 'المحادثة: الرسالة ٣',
                'methods_title' => 'عنوان «قنوات مباشرة»', 'form_title' => 'عنوان «أرسل رسالة»', 'location_title' => 'عنوان الموقع',
                'place' => 'المقر (محافظة مسقط)', 'country' => 'الدولة', 'place_text' => 'نص تحت المقر',
            ],
        ],
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
        'awards' => [
            'before' => ['award-volunteer'],
            'after' => ['awards-hero', 'scroll-cue', 'desktop-hero-height', 'awards-cards-desktop', 'awards-hero-words', 'phone-port-awards'],
        ],
        'award-culture' => [
            'before' => ['home-aisha', 'leader-word', 'minister-design-5', 'award'],
            'after' => ['award-details-desktop'],
        ],
        'award-volunteer' => [
            'before' => ['award-volunteer', 'award-volunteer-media'],
            'after' => ['award-volunteer-desktop', 'phone-port-volunteer'],
        ],
        'award-architecture' => [
            'before' => ['bha-concepts-base', 'bha-c2', 'bha-final'],
            'after' => ['scroll-cue', 'desktop-hero-height', 'bha-hero-motion', 'bha-hero-intro', 'phone-port-bilarab', 'phone-arc-motion'],
        ],
        'org-news' => [
            'before' => ['institutions-page', 'org-news', 'article-page'],
            'after' => [],
        ],
        'achievement' => [
            'before' => ['achievement-detail', 'profile-idcard', 'profile-enhance', 'achievement-enhance'],
            'after' => [],
        ],
        'lab' => [
            'before' => ['science-page', 'lab-v2'],
            'after' => ['lab-hero-desktop', 'lab-sections-desktop', 'lab-showcase', 'phone-port-lab'],
        ],
        'news' => [
            'before' => ['home-news'],
            'after' => [],
        ],
        'magazine' => [
            'before' => ['articles-page'],
            'after' => ['articles-hero-desktop', 'articles-hero-wall', 'phone-port-articles'],
        ],
        'article' => [
            'before' => ['articles-page', 'article-page'],
            'after' => [],
        ],
        'writers' => [
            'before' => ['articles-page', 'writers-page'],
            'after' => [],
        ],
        'search' => [
            'before' => ['achievements-page', 'achievers-page', 'articles-page'],
            'after' => [],
        ],
        'contact' => [
            'before' => ['contact'],
            'after' => ['contact-hero-desktop', 'phone-port-contact'],
        ],
        'about' => [
            'main' => false,
            'before' => ['about-roadmap', 'about-page', 'about-footer'],
            'after' => ['about-hero-desktop', 'phone-port-about', 'about-page-2', 'phone-port-journey'],
        ],
        'inspiring' => [
            'before' => ['inspiring-page', 'inspiring-strip'],
            'after' => ['inspiring-desktop-hero', 'inspiring-hero-v2', 'inspiring-hero-polish', 'inspiring-hero-intro', 'phone-port-inspiring', 'inspiring-hero-text', 'inspiring-himam'],
        ],
        'lab-all' => [
            'before' => ['science-page', 'lab-v2', 'lab-all-desktop'],
            'after' => ['phone-port-lab', 'phone-port-lab-all'],
        ],
        'lab-item' => [
            'before' => ['science-page', 'lab-v2'],
            'after' => ['phone-port-lab'],
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
