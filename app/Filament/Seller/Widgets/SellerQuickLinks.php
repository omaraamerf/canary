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

    public function getHeading(): ?string { return __('إدارة حساب البائع'); }

    public function getDescription(): ?string { return __('وصول سريع إلى صفحاتك وملخص نشاطك.'); }

    protected function getStats(): array
    {
        /** @var User $seller */
        $seller = Filament::auth()->user();
        $stats = app(SellerDashboardStatsService::class)->summary($seller);

        return [
            Stat::make(__('طيوري'), number_format($stats['birds']))
                ->description(number_format($stats['available_birds']).__(' طائر متاح'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary')
                ->url(BirdResource::getUrl()),
            Stat::make(__('إضافة طائر'), __('إعلان جديد'))
                ->description(__('أضف الصور وبيانات الطائر'))
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('warning')
                ->url(BirdResource::getUrl('create')),
            Stat::make(__('الطلبات'), number_format($stats['pending_orders']))
                ->description(__('طلبات جديدة تحتاج المتابعة'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($stats['pending_orders'] > 0 ? 'danger' : 'success')
                ->url(OrderResource::getUrl()),
            Stat::make(__('الملف الشخصي'), __('تعديل البيانات'))
                ->description(number_format($stats['delivered_orders']).__(' طلبات تم تسليمها'))
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info')
                ->url(route('filament.seller.auth.profile')),
        ];
    }
}
