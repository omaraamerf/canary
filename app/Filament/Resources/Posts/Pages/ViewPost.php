<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Filament\Shared\Actions\EntityActions;
use Filament\Resources\Pages\ViewRecord;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PostsTable::toggleStatusAction(),
            PostsTable::openOnSiteAction(),
            EntityActions::delete(),
            EntityActions::forceDelete(),
            EntityActions::restore(),
        ];
    }
}
