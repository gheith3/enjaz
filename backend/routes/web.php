<?php

use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\AchievementController;
use App\Http\Controllers\Site\AchieverController;
use App\Http\Controllers\Site\AwardController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\InspiringController;
use App\Http\Controllers\Site\LabController;
use App\Http\Controllers\Site\MagazineController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\OrganizationController;
use App\Http\Controllers\Site\RedirectController;
use App\Http\Controllers\Site\RobotsController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Middlewares\CacheResponse;

/*
 * Public site. Pages are cached as full responses for visitors (spatie/laravel-responsecache) and the
 * cache is cleared whenever content changes in the dashboard. Livewire lists keep their state in the
 * query string, so every filtered view is its own cached page.
 */
Route::middleware(CacheResponse::class)->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{achievement:slug}', [AchievementController::class, 'show'])->name('achievements.show');

    Route::get('/achievers', [AchieverController::class, 'index'])->name('achievers.index');
    Route::get('/achievers/{achiever:slug}', [AchieverController::class, 'show'])->name('achievers.show');

    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/universities', [OrganizationController::class, 'universities'])->name('universities.index');
    Route::get('/organizations/{organization:slug}', [OrganizationController::class, 'show'])->name('organizations.show');
    Route::get('/organizations/{organization:slug}/achievements', [OrganizationController::class, 'achievements'])->name('organizations.achievements');
    Route::get('/organizations/{organization:slug}/people', [OrganizationController::class, 'people'])->name('organizations.people');

    Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');
    Route::get('/awards/{award:slug}', [AwardController::class, 'show'])->name('awards.show');
    Route::get('/inspiring', InspiringController::class)->name('inspiring');
    Route::get('/about', AboutController::class)->name('about');
    Route::get('/contact', ContactController::class)->name('contact');
    Route::get('/search', SearchController::class)->name('search');

    Route::get('/lab', [LabController::class, 'index'])->name('lab.index');
    Route::get('/lab/{segment}', [LabController::class, 'list'])->whereIn('segment', ['inventions', 'publications'])->name('lab.list');
    Route::get('/lab/{item:slug}', [LabController::class, 'show'])->name('lab.show');

    Route::get('/magazine', [MagazineController::class, 'index'])->name('magazine.index');
    Route::get('/magazine/{article:slug}', [MagazineController::class, 'show'])->name('magazine.show');
    Route::get('/writers', [MagazineController::class, 'writers'])->name('magazine.writers');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');
});

// Read counter of an article (the article page itself is cached).
Route::post('/magazine/{article:slug}/view', [MagazineController::class, 'view'])->middleware('throttle:30,1')->name('magazine.view');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

// Old addresses (the design's *.html pages, renamed slugs): table `redirects`, edited in the dashboard.
Route::fallback(RedirectController::class);
