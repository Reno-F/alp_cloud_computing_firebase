<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// 📌 Catalog (home, detail, about, contact)
Route::controller(CatalogController::class)->group(function() {
    Route::match(['get', 'post'], '/', 'list')->name('catalog');
    Route::get('/detail/{id}', 'detail')->name('catalog-detail');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
});

// 📌 User signup & login
Route::controller(UserController::class)->group(function() {
    Route::match(['get', 'post'], '/signup', 'signup')->name('signup');
    Route::match(['get', 'post'], '/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

// 📌 Product (CRUD) - Hanya user login yang bisa akses
Route::middleware('check.login')->controller(ProductController::class)->group(function() {
    Route::match(['get', 'post'], '/product/create', 'create')->name('product-create');
    Route::match(['get', 'post'], '/product/{id}/edit', 'edit')->name('product-edit');
    Route::delete('/product/{id}/delete', 'delete')->name('product-delete');
});

// 📌 Cart (keranjang)
Route::middleware('check.login')->controller(CartController::class)->group(function() {
    Route::post('/cart/add/{id}', 'add')->name('cart-add');
    Route::post('/cart/remove/{id}', 'remove')->name('cart-remove');
    Route::post('/cart/decrement/{id}', 'decrement')->name('cart-decrement');
    Route::post('/cart/increment/{id}', 'increment')->name('cart-increment');
    Route::get('/checkout', 'checkout')->name('checkout');
    Route::post('/checkout/process', 'process')->name('checkout.process');
});

// 📌 Review (create, edit, delete memerlukan auth)
Route::middleware('check.login')->controller(ReviewController::class)->group(function() {
    Route::post('/products/{product}/reviews', 'store')->name('reviews.store');
    Route::get('/products/{product}/reviews', 'index')->name('reviews.index');
    Route::get('/reviews/{review}/edit', 'edit')->name('reviews.edit');
    Route::put('/reviews/{review}', 'update')->name('reviews.update');
    Route::delete('/reviews/{review}', 'destroy')->name('reviews.destroy');
});
