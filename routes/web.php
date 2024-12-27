<?php

use App\Livewire\ContactUs;
use App\Livewire\SingleAchievement;
use Illuminate\Support\Facades\Route;
use App\Livewire\SingleAchieverHero;


Route::get('/', function () {
    return view('welcome'); // This should point to your welcome view
})->name('home');

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

Route::get('/achievers/{achiever}', SingleAchieverHero::class)
    ->name('achievers.show');

Route::get('/achievement/{achievement}', SingleAchievement::class)
    ->name('achievement.show');




