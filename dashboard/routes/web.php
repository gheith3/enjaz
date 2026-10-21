<?php

use App\Livewire\Pages;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Public site routes
|--------------------------------------------------------------------------
| Livewire full-page components (see design/DESIGN.md section 10.7).
| The Filament admin panel lives at /dashboard and is registered separately.
*/

Route::get('/', Pages\Home::class)->name('home');

Route::get('/about', Pages\About::class)->name('about');

Route::get('/achievers', Pages\Achievers::class)->name('achievers.index');
Route::get('/achievers/{publicFigure:slug}', Pages\AchieverShow::class)->name('achievers.show');

Route::get('/achievements', Pages\Achievements::class)->name('achievements.index');
Route::get('/achievements/{achievement:slug}', Pages\AchievementShow::class)->name('achievements.show');

Route::get('/institutions', Pages\Institutions::class)->name('institutions.index');
Route::get('/institutions/{organization:slug}', Pages\InstitutionShow::class)->name('institutions.show');

Route::get('/media', Pages\Media::class)->name('media');
Route::get('/media/videos', Pages\MediaVideos::class)->name('media.videos');
Route::get('/media/publications', Pages\MediaPublications::class)->name('media.publications');
Route::get('/media/images-albums', Pages\MediaImageAlbums::class)->name('media.images-albums');
Route::get('/media/images-albums/{imageMedia:slug}', Pages\MediaImageAlbumShow::class)->name('media.images-albums.show');

Route::get('/news/{news:slug}', Pages\NewsShow::class)->name('news.show');

Route::get('/contact', Pages\Contact::class)->name('contact');

// Serve media from the default disk (S3/MinIO) through the app origin, so
// pages loaded over https://enjaz.test don't hit mixed-content blocking.
Route::get('/cdn/{path}', function (string $path) {
    abort_unless(Storage::exists($path), 404);

    return Storage::response($path);
})->where('path', '.*')->name('cdn');
