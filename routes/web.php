<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BirdController as AdminBirdController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\BirdController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/birds', [BirdController::class, 'index'])->name('birds.index');
Route::get('/birds/{bird:slug}', [BirdController::class, 'show'])->name('birds.show');
Route::post('/birds/{bird:slug}/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order:reference}/received', [OrderController::class, 'received'])->name('orders.received');
Route::view('/about', 'pages.about')->name('about');
Route::view('/policy', 'pages.policy')->name('policy');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('birds', AdminBirdController::class)->except('show');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
});
