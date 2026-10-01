<?php

namespace App\Filament\Resources\Roles;

use App\Enums\UserRole;
use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-shield-check';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Setup;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string { return __('دور'); }

    public static function getPluralModelLabel(): string { return __('الأدوار'); }

    public static function getNavigationLabel(): string { return __('الأدوار'); }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? UserRole::labelFor($record->name) : null;
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
