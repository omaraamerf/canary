<?php

namespace App\Enums;

enum Permission: string
{
    case AccessAdminPanel = 'panels.admin.access';
    case AccessSellerPanel = 'panels.seller.access';

    case ViewAnyBirds = 'birds.viewAny';
    case ViewOwnBirds = 'birds.viewOwn';
    case ViewBird = 'birds.view';
    case CreateBird = 'birds.create';
    case UpdateAnyBird = 'birds.updateAny';
    case UpdateOwnBird = 'birds.updateOwn';
    case DeleteAnyBird = 'birds.deleteAny';
    case DeleteOwnBird = 'birds.deleteOwn';
    case RestoreBird = 'birds.restore';
    case ForceDeleteBird = 'birds.forceDelete';
    case ApproveBird = 'birds.approve';

    case ViewAnyOrders = 'orders.viewAny';
    case ViewOwnOrders = 'orders.viewOwn';
    case ViewOrder = 'orders.view';
    case UpdateAnyOrder = 'orders.updateAny';
    case UpdateOwnOrder = 'orders.updateOwn';

    case ViewSellers = 'sellers.view';
    case UpdateSellers = 'sellers.update';
    case ManageRegions = 'regions.manage';
    case ManageBreeds = 'breeds.manage';
    case ManageArticles = 'articles.manage';
    case ManageArticleCategories = 'articleCategories.manage';
    case ManageSettings = 'settings.manage';
    case ManageCommunity = 'community.manage';
    case ManageRoles = 'roles.manage';

    public function label(): string
    {
        return match ($this) {
            self::AccessAdminPanel => __('دخول لوحة الإدارة'),
            self::AccessSellerPanel => __('دخول لوحة البائع'),
            self::ViewAnyBirds => __('عرض كل الطيور'),
            self::ViewOwnBirds => __('عرض طيوره فقط'),
            self::ViewBird => __('عرض تفاصيل طائر'),
            self::CreateBird => __('إضافة طائر'),
            self::UpdateAnyBird => __('تعديل أي طائر'),
            self::UpdateOwnBird => __('تعديل طيوره فقط'),
            self::DeleteAnyBird => __('حذف أي طائر'),
            self::DeleteOwnBird => __('حذف طيوره فقط'),
            self::RestoreBird => __('استرجاع طائر محذوف'),
            self::ForceDeleteBird => __('حذف طائر نهائياً'),
            self::ApproveBird => __('الموافقة على الطيور'),
            self::ViewAnyOrders => __('عرض كل الطلبات'),
            self::ViewOwnOrders => __('عرض طلباته فقط'),
            self::ViewOrder => __('عرض تفاصيل طلب'),
            self::UpdateAnyOrder => __('تحديث أي طلب'),
            self::UpdateOwnOrder => __('تحديث طلباته فقط'),
            self::ViewSellers => __('عرض البائعين'),
            self::UpdateSellers => __('إدارة البائعين'),
            self::ManageRegions => __('إدارة الدول والمناطق'),
            self::ManageBreeds => __('إدارة السلالات'),
            self::ManageArticles => __('إدارة المقالات'),
            self::ManageArticleCategories => __('إدارة أقسام المقالات'),
            self::ManageSettings => __('إدارة إعدادات الموقع'),
            self::ManageCommunity => __('إدارة المجتمع'),
            self::ManageRoles => __('إدارة الأدوار والصلاحيات'),
        };
    }

    public static function labelFor(string $name): string
    {
        return self::tryFrom($name)?->label() ?? $name;
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $permission): array => [$permission->value => $permission->label()])
            ->all();
    }
}
