<?php

namespace App\Filament\Resources\Breeds;

use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Breeds\Pages\CreateBreed;
use App\Filament\Resources\Breeds\Pages\EditBreed;
use App\Filament\Resources\Breeds\Pages\ListBreeds;
use App\Filament\Resources\Breeds\Schemas\BreedForm;
use App\Filament\Resources\Breeds\Tables\BreedsTable;
use App\Models\Breed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BreedResource extends Resource
{
    protected static ?string $model = Breed::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-tags';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Setup;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return __('سلالة'); }

    public static function getPluralModelLabel(): string { return __('السلالات'); }

    public static function getNavigationLabel(): string { return __('السلالات'); }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BreedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BreedsTable::configure($table);
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
            'index' => ListBreeds::route('/'),
            'create' => CreateBreed::route('/create'),
            'edit' => EditBreed::route('/{record}/edit'),
        ];
    }
}
