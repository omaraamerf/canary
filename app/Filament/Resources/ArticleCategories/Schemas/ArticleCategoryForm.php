<?php

namespace App\Filament\Resources\ArticleCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArticleCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('اسم القسم'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('الوصف'))
                    ->columnSpanFull(),
                Textarea::make('image')
                    ->label(__('رابط الصورة'))
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label(__('الترتيب'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
