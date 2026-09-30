<?php

namespace App\Filament\Resources\ArticleCategories\Pages;

use App\Filament\Resources\ArticleCategories\ArticleCategoryResource;
use App\Filament\Shared\Actions\EntityActions;
use App\Services\CatalogService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditArticleCategory extends EditRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EntityActions::delete(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(CatalogService::class)->saveArticleCategory($data, $record);
    }
}
