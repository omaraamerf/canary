<?php

namespace App\Enums;

enum SettingKey: string
{
    case SellerApprovalRequired = 'seller_approval_required';
    case ListingApprovalRequired = 'listing_approval_required';
    case GuideEnabled = 'guide_enabled';
    case CommunityEnabled = 'community_enabled';
}
