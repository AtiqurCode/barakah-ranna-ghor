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
    abort_unless(in_array($locale, config('barakah.locales'), true), 404);

    session(['locale' => $locale]);

    return back();
})->middleware('throttle:30,1')->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authenticated app
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::livewire('products', 'pages::admin.products')->name('admin.products');
        Route::livewire('products/create', 'pages::admin.product-create')->name('admin.products.create');
        Route::livewire('products/{product}/edit', 'pages::admin.product-edit')->name('admin.products.edit');
        Route::livewire('messages', 'pages::admin.messages')->name('admin.messages');
        Route::livewire('subscribers', 'pages::admin.subscribers')->name('admin.subscribers');
    });
});

require __DIR__.'/settings.php';
