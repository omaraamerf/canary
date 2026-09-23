<?php

use App\Http\Controllers\BirdController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegionSelectionController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\SellerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/birds', [BirdController::class, 'index'])->name('birds.index');
Route::get('/birds/{bird:slug}', [BirdController::class, 'show'])->name('birds.show');
Route::post('/birds/{bird:slug}/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/track', [OrderController::class, 'track'])->name('orders.track');
Route::post('/orders/track', [OrderController::class, 'lookup'])->middleware('throttle:10,1')->name('orders.track.lookup');
Route::get('/orders/track/{order:reference}', [OrderController::class, 'showTracking'])->name('orders.track.show');
Route::get('/orders/{order:reference}/received', [OrderController::class, 'received'])->name('orders.received');
Route::view('/about', 'pages.about')->name('about');
Route::view('/policy', 'pages.policy')->name('policy');
Route::view('/start-selling', 'pages.start-selling')->name('start-selling');
Route::post('/region', [RegionSelectionController::class, 'store'])->name('region.select');
Route::get('/sellers/{seller}', [SellerProfileController::class, 'show'])->name('sellers.show');
Route::get('/guide', [GuideController::class, 'index'])->name('guide.index');
Route::get('/guide/{category:slug}', [GuideController::class, 'category'])->name('guide.category');
Route::get('/guide/{category:slug}/{article:slug}', [GuideController::class, 'show'])->name('guide.show');

Route::middleware('guest')->group(function () {
    Route::get('/seller/register', [SellerAuthController::class, 'register'])->name('seller.register');
    Route::post('/seller/register', [SellerAuthController::class, 'storeRegistration'])->name('seller.register.store');
});
