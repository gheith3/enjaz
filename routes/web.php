<?php

use Illuminate\Support\Facades\Route;


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


