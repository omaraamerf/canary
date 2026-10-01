<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\Permission;
use App\Enums\UserRole;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('اسم الدور'))
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : UserRole::labelFor($state))
                    ->disabled(fn (?Role $record): bool => $record !== null && UserRole::isBuiltIn($record->name))
                    ->helperText(fn (?Role $record): ?string => $record !== null && UserRole::isBuiltIn($record->name)
                        ? __('اسم الدور الأساسي ثابت، ويمكنك تعديل صلاحياته فقط.')
                        : null)
                    ->required()
                    ->maxLength(100)
                    ->unique(Role::class, 'name', ignoreRecord: true),
                CheckboxList::make('permissions')
                    ->label(__('الصلاحيات'))
                    ->options(Permission::options())
                    ->columns(2)
                    ->bulkToggleable()
                    ->columnSpanFull(),
            ]);
    }
}
