<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-store';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Market;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string { return __('بائع'); }

    public static function getPluralModelLabel(): string { return __('البائعون'); }

    public static function getNavigationLabel(): string { return __('البائعون'); }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /** Count of what is waiting, shown beside the sidebar item. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', UserStatus::Pending->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('بانتظار الموافقة');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->role(UserRole::Seller->value)
            ->with(['sellerProfile.region'])
            ->withCount('birds');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}
