<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (public)
|--------------------------------------------------------------------------
*/

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/products', 'pages::products')->name('products');
Route::livewire('/products/{product:slug}', 'pages::product')->name('product');
Route::livewire('/about', 'pages::about')->name('about');
Route::livewire('/contact', 'pages::contact')->name('contact');

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, config('barakah.locales'), true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authenticated app
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
