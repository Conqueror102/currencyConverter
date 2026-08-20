<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('docs', 'docs')->name('docs');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('dashboard/guide', 'guide')
        ->middleware('can:access-admin')
        ->name('admin.guide');
});

require __DIR__.'/settings.php';
