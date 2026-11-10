<?php

namespace App\Support\DesignImport;

/**
 * Where each design page lives on the new site, and which fixed page (config/site.php) it feeds.
 * Record pages (profile.html, award.html ...) get their redirects in the phase that imports the records.
 */
class DesignMap
{
    public const PATHS = [
        'index.html' => '/',
        'achievements.html' => '/achievements',
        'achievers.html' => '/achievers',
        'inspiring.html' => '/inspiring',
        'awards.html' => '/awards',
        'bilarab-award.html' => '/awards/bilarab',
        'universities.html' => '/universities',
        'institutions.html' => '/organizations',
        'science.html' => '/lab',
        'lab-all.html' => '/lab',
        'news.html' => '/news',
        'articles.html' => '/magazine',
        'writers.html' => '/writers',
        'about.html' => '/about',
        'about-journey-1.html' => '/about',
        'contact.html' => '/contact',
    ];

    public const PAGE_KEYS = [
        'home' => 'index.html',
        'achievements' => 'achievements.html',
        'achievers' => 'achievers.html',
        'inspiring' => 'inspiring.html',
        'awards' => 'awards.html',
        'organizations' => 'institutions.html',
        'universities' => 'universities.html',
        'lab' => 'science.html',
        'news' => 'news.html',
        'magazine' => 'articles.html',
        'writers' => 'writers.html',
        'about' => 'about.html',
        'contact' => 'contact.html',
    ];

    /**
     * New path for a design link (keeps #fragments; unknown pages map to null).
     */
    public static function path(string $href): ?string
    {
        $href = preg_replace('#^\./#', '', $href);
        [$file] = explode('?', explode('#', $href)[0]);

        return self::PATHS[$file] ?? null;
    }
}
