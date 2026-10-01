<?php

namespace App\Filament\Resources\Birds;

use App\Enums\ApprovalStatus;
use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Birds\Pages\CreateBird;
use App\Filament\Resources\Birds\Pages\EditBird;
use App\Filament\Resources\Birds\Pages\ListBirds;
use App\Filament\Resources\Birds\Schemas\BirdForm;
use App\Filament\Resources\Birds\Tables\BirdsTable;
use App\Models\Bird;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BirdResource extends Resource
{
    protected static ?string $model = Bird::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-bird';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Market;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return __('طائر');
    }

    public static function getPluralModelLabel(): string
    {
        return __('الطيور');
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

    /** Count of what is waiting, shown beside the sidebar item. */
    public static function getNavigationBadge(): ?string
    {
        $count = Bird::query()->where('approval_status', ApprovalStatus::Pending->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('بانتظار المراجعة');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['breed', 'seller', 'media']);
    }
}
