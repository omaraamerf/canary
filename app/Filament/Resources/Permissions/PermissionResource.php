<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-key-round';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Setup;

    protected static ?int $navigationSort = 6;

    public static function getModelLabel(): string { return __('صلاحية'); }

    public static function getPluralModelLabel(): string { return __('الصلاحيات'); }

    public static function getNavigationLabel(): string { return __('الصلاحيات'); }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
        ];
    }
}
