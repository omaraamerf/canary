<?php

use App\Enums\Permission;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackSiteVisit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [SetLocale::class, TrackSiteVisit::class]);

        $middleware->redirectGuestsTo(fn (Request $request) => match (true) {
            $request->is('seller*') => '/seller/login',
            $request->is('admin*') => '/admin/login',
            default => route('login'),
        });
        $middleware->redirectUsersTo(fn (Request $request) => match (true) {
            (bool) $request->user()?->can(Permission::AccessSellerPanel->value) => '/seller',
            (bool) $request->user()?->can(Permission::AccessAdminPanel->value) => '/admin',
            default => route('home'),
        });

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
