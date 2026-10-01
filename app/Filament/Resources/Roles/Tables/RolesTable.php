<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Enums\UserRole;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('الدور'))
                    ->formatStateUsing(fn (string $state): string => UserRole::labelFor($state))
                    ->description(fn (Role $record): ?string => match (true) {
                        $record->name === UserRole::Admin->value => __('يملك كل الصلاحيات ولا يمكن تعديله.'),
                        UserRole::isBuiltIn($record->name) => __('دور أساسي في الموقع.'),
                        default => null,
                    })
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->label(__('الصلاحيات'))
                    ->counts('permissions')
                    ->badge()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('المستخدمون'))
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('آخر تحديث'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
