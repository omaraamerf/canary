<?php

namespace App\Filament\Seller\Widgets;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Filament\Seller\Resources\Orders\OrderResource;
use App\Models\User;
use App\Services\SellerDashboardStatsService;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SellerQuickLinks extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'إدارة حساب البائع';

    protected ?string $description = 'وصول سريع إلى صفحاتك وملخص نشاطك.';

    protected function getStats(): array
    {
        /** @var User $seller */
        $seller = Filament::auth()->user();
        $stats = app(SellerDashboardStatsService::class)->summary($seller);

        return [
            Stat::make('طيوري', number_format($stats['birds']))
                ->description(number_format($stats['available_birds']).' طائر متاح')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary')
                ->url(BirdResource::getUrl()),
            Stat::make('إضافة طائر', 'إعلان جديد')
                ->description('أضف الصور وبيانات الطائر')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('warning')
                ->url(BirdResource::getUrl('create')),
            Stat::make('الطلبات', number_format($stats['pending_orders']))
                ->description('طلبات جديدة تحتاج المتابعة')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($stats['pending_orders'] > 0 ? 'danger' : 'success')
                ->url(OrderResource::getUrl()),
            Stat::make('الملف الشخصي', 'تعديل البيانات')
                ->description(number_format($stats['delivered_orders']).' طلبات تم تسليمها')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info')
                ->url(route('filament.seller.auth.profile')),
        ];
    }
}
