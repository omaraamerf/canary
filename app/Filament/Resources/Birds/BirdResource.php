<?php

namespace App\Filament\Resources\Birds;

use App\Filament\Resources\Birds\Pages\CreateBird;
use App\Filament\Resources\Birds\Pages\EditBird;
use App\Filament\Resources\Birds\Pages\ListBirds;
use App\Filament\Resources\Birds\Schemas\BirdForm;
use App\Filament\Resources\Birds\Tables\BirdsTable;
use App\Models\Bird;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BirdResource extends Resource
{
    protected static ?string $model = Bird::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return 'طائر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الطيور';
    }

    public static function form(Schema $schema): Schema
    {
        return BirdForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BirdsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBirds::route('/'),
            'create' => CreateBird::route('/create'),
            'edit' => EditBird::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['breed', 'seller', 'media']);
    }
}
