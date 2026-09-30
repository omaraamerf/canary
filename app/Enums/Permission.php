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
}
