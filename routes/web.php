<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ArticleCategoryController as AdminArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\BirdController as AdminBirdController;
use App\Http\Controllers\Admin\BreedController as AdminBreedController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RegionController as AdminRegionController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\BirdController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegionSelectionController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Seller\BirdController as SellerBirdController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileEditController;
use App\Http\Controllers\SellerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/birds', [BirdController::class, 'index'])->name('birds.index');
Route::get('/birds/{bird:slug}', [BirdController::class, 'show'])->name('birds.show');
Route::post('/birds/{bird:slug}/orders', [OrderController::class, 'store'])->name('orders.store');
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
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
    Route::get('/seller/login', [SellerAuthController::class, 'create'])->name('seller.login');
    Route::post('/seller/login', [SellerAuthController::class, 'store'])->name('seller.login.store');
    Route::get('/seller/register', [SellerAuthController::class, 'register'])->name('seller.register');
    Route::post('/seller/register', [SellerAuthController::class, 'storeRegistration'])->name('seller.register.store');
});

Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {
    Route::get('/', SellerDashboardController::class)->name('dashboard');
    Route::resource('birds', SellerBirdController::class)->except('show');
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/profile', [SellerProfileEditController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SellerProfileEditController::class, 'update'])->name('profile.update');
    Route::post('/logout', [SellerAuthController::class, 'destroy'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('birds', AdminBirdController::class)->except('show');
    Route::patch('/birds/{bird}/approval', [AdminBirdController::class, 'updateApproval'])->name('birds.approval');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
    Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
    Route::patch('/sellers/{seller}/status', [AdminSellerController::class, 'updateStatus'])->name('sellers.status');
    Route::resource('regions', AdminRegionController::class)->only(['index', 'store', 'update']);
    Route::resource('breeds', AdminBreedController::class)->only(['index', 'store', 'update']);
    Route::resource('guide-categories', AdminArticleCategoryController::class)->only(['index', 'store', 'update']);
    Route::resource('guide-articles', AdminArticleController::class)->except(['show', 'destroy']);
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
