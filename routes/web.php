<?php

use App\Livewire\ContactUs;
use App\Livewire\InstitutePage;
use App\Livewire\Pages\AchievementsPage;
use App\Livewire\Pages\AchieversPage;
use App\Livewire\Pages\HomePage;
use App\Livewire\Pages\OrganizationsPage;
use App\Livewire\SingleAchievement;
use Illuminate\Support\Facades\Route;
use App\Livewire\SingleAchieverHero;

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

// Alternatively, if you want to access it via /home
Route::get('/home', function () {
    return view('welcome');
})->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/achievers', function () {
    return view('achievers');
})->name('achievers');

Route::get('/institutes', function () {
    return view('institutes');
})->name('institutes');



Route::get('/contact-us', ContactUs::class)->name('contact-us');








