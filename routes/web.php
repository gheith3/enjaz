<?php

use App\Livewire\ContactUs;
use App\Livewire\InstitutePage;
use App\Livewire\Pages\AchievementsPage;
use App\Livewire\Pages\AchieversPage;
use App\Livewire\Pages\HomePage;
use App\Livewire\Pages\ImagesAlbumPage;
use App\Livewire\Pages\ImagesAlbumsPage;
use App\Livewire\Pages\MediaPage;
use App\Livewire\Pages\OrganizationsPage;
use App\Livewire\Pages\PublicationsPage;
use App\Livewire\Pages\VideosPage;
use App\Livewire\SingleAchievement;
use Illuminate\Support\Facades\Route;
use App\Livewire\SingleAchieverHero;
use App\Http\Controllers\AlbumController;

Route::get('/', HomePage::class)
    ->name('home');

Route::get('/achievements', AchievementsPage::class)
    ->name('achievements');
Route::get('/achievement/{achievement}', SingleAchievement::class)
    ->name('achievement.show');

Route::get('/organizations', OrganizationsPage::class)
    ->name('organizations');
Route::get('/organizations/{institute}', InstitutePage::class)
    ->name('institutes.show');

Route::get('/achievers', AchieversPage::class)
    ->name('achievers');

Route::get('/achievers/{achiever}', SingleAchieverHero::class)
    ->name('achievers.show');


Route::get('/media', MediaPage::class)
    ->name('media');

Route::get('/media/publications', PublicationsPage::class)
    ->name('media.publications');

Route::get('/media/videos', VideosPage::class)
    ->name('media.videos');

Route::get('/media/images-albums', ImagesAlbumsPage::class)
    ->name('media.images-albums');

Route::get('/media/images-albums/{id}', ImagesAlbumPage::class)
    ->name('media.images-albums.show');

Route::get('/about', function () {
    return view('about');
})->name('about');


Route::get('/institutes', function () {
    return view('institutes');
})->name('institutes');



Route::get('/contact-us', ContactUs::class)->name('contact-us');

Route::get('/images-albums', App\Livewire\Pages\ImagesAlbumsPage::class)->name('images-albums');
Route::get('/album/{album}', [AlbumController::class, 'show'])->name('album.show');








