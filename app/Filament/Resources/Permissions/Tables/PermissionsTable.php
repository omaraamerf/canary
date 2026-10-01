<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Enums\Permission as PermissionName;
use App\Enums\UserRole;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('الصلاحية'))
                    ->formatStateUsing(fn (string $state): string => PermissionName::labelFor($state))
                    ->description(fn (Permission $record): string => $record->name)
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('الأدوار التي تملكها'))
                    ->formatStateUsing(fn (string $state): string => UserRole::labelFor($state))
                    ->badge()
                    ->placeholder(__('لا يوجد'))
                    ->wrap(),
            ])
            ->paginated(false);
    }
}
