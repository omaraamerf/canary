<?php

namespace App\Providers;

use App\Enums\SettingKey;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Support\LocationOptions;
use App\Support\MarketplaceLocation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(LocationOptions::class);
        $this->app->scoped(MarketplaceLocation::class, fn ($app) => new MarketplaceLocation($app['session.store']));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'post' => Post::class,
            'comment' => Comment::class,
        ]);

        Paginator::defaultView('partials.pagination');

        View::composer(['layouts.app', 'guide.partials.sidebar'], function ($view) {
            $location = app(MarketplaceLocation::class);

            $view->with([
                'marketplaceLocation' => $location,
                'regionChosen' => session('marketplace_region_chosen', false),
                'guideEnabled' => Setting::boolean('guide_enabled', true),
                'communityEnabled' => Setting::boolean(SettingKey::CommunityEnabled->value, true),
            ]);
        });
    }
}
