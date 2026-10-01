<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BirdController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegionSelectionController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Middleware\EnsureCommunityEnabled;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

Route::get('/', HomeController::class)->name('home');
Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['ar', 'en'])
    ->name('locale.switch');
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

Route::middleware(EnsureCommunityEnabled::class)->prefix('community')->name('community.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('show');

    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/posts', [PostController::class, 'store'])->middleware('throttle:6,1')->name('store');
        Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->middleware('throttle:15,1')->name('comments.store');
        Route::post('/posts/{post:slug}/comments/{comment}/accept', [PostController::class, 'accept'])->name('comments.accept');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/seller/register', [SellerAuthController::class, 'register'])->name('seller.register');
    Route::post('/seller/register', [SellerAuthController::class, 'storeRegistration'])->name('seller.register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'show'])->name('show');
    Route::get('/edit', [AccountController::class, 'edit'])->name('edit');
    Route::put('/', [AccountController::class, 'update'])->middleware('throttle:10,1')->name('update');
    Route::put('/password', [AccountController::class, 'updatePassword'])->middleware('throttle:6,1')->name('password');
});

Route::get('/members/{user}', [MemberController::class, 'show'])->name('members.show');

// Component gallery for reviewing the design system; never registered outside local development.
if (app()->environment('local')) {
    Route::get('/_ui', function () {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['demo_email' => __('validation.email', ['attribute' => __('ui.common.email')])])));

        return view('dev.ui');
    })->name('dev.ui');
}
