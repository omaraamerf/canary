<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Birds\BirdResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Settings\SettingResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.quick-actions';

    protected function getViewData(): array
    {
        return [
            'heading' => __('اختصارات'),
            'actions' => [
                ['label' => __('إضافة طائر'), 'icon' => 'lucide-circle-plus', 'url' => BirdResource::getUrl('create')],
                ['label' => __('كتابة مقال'), 'icon' => 'lucide-square-pen', 'url' => ArticleResource::getUrl('create')],
                ['label' => __('استفسارات المجتمع'), 'icon' => 'lucide-messages-square', 'url' => PostResource::getUrl()],
                ['label' => __('إعدادات الموقع'), 'icon' => 'lucide-settings', 'url' => SettingResource::getUrl()],
            ],
        ];
    }
}
