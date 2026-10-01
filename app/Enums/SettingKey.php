<?php

namespace App\Enums;

enum SettingKey: string
{
    case SellerApprovalRequired = 'seller_approval_required';
    case ListingApprovalRequired = 'listing_approval_required';
    case GuideEnabled = 'guide_enabled';
    case CommunityEnabled = 'community_enabled';

    public function label(): string
    {
        return match ($this) {
            self::SellerApprovalRequired => __('مراجعة البائعين الجدد'),
            self::ListingApprovalRequired => __('مراجعة الإعلانات الجديدة'),
            self::GuideEnabled => __('قسم الدليل والمقالات'),
            self::CommunityEnabled => __('قسم المجتمع'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SellerApprovalRequired => __('عند التفعيل، لا يستطيع البائع الجديد النشر حتى توافق الإدارة على حسابه.'),
            self::ListingApprovalRequired => __('عند التفعيل، لا يظهر الإعلان الجديد في الموقع حتى توافق الإدارة عليه.'),
            self::GuideEnabled => __('عند الإيقاف، يختفي الدليل والمقالات من الموقع والقائمة.'),
            self::CommunityEnabled => __('عند الإيقاف، يختفي قسم المجتمع والاستفسارات من الموقع.'),
        };
    }

    public static function labelFor(string $key): string
    {
        return self::tryFrom($key)?->label() ?? $key;
    }

    public static function descriptionFor(string $key): ?string
    {
        return self::tryFrom($key)?->description();
    }
}
