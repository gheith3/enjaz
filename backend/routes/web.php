<?php

use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\RedirectController;
use App\Http\Controllers\Site\RobotsController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Middlewares\CacheResponse;

/*
 * Public site. Pages are cached as full responses for visitors (spatie/laravel-responsecache) and the
 * cache is cleared whenever content changes in the dashboard.
 */
Route::middleware(CacheResponse::class)->group(function () {
    Route::get('/', HomeController::class)->name('home');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

// Old addresses (the design's *.html pages, renamed slugs): table `redirects`, edited in the dashboard.
Route::fallback(RedirectController::class);
