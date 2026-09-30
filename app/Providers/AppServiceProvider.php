<?php

namespace App\Providers;

use App\Enums\SettingKey;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Region;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

        View::composer('layouts.app', function ($view) {
            $selectedRegionId = session('marketplace_region_id');

            $view->with([
                'navigationRegions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
                'selectedRegion' => $selectedRegionId ? Region::find($selectedRegionId) : null,
                'regionChosen' => session('marketplace_region_chosen', false),
                'guideEnabled' => Setting::boolean('guide_enabled', true),
                'communityEnabled' => Setting::boolean(SettingKey::CommunityEnabled->value, true),
            ]);
        });
    }
}
