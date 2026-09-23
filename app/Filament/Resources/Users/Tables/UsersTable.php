<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\SellerService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                TextColumn::make('sellerProfile.display_name')->label('اسم المتجر')->searchable(),
                TextColumn::make('email')->label('البريد الإلكتروني')->searchable(),
                TextColumn::make('phone')->label('الهاتف')->searchable(),
                TextColumn::make('sellerProfile.region.name')->label('المنطقة'),
                TextColumn::make('birds_count')->label('عدد الطيور')->numeric()->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state),
                TextColumn::make('created_at')->label('تاريخ التسجيل')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(self::statusOptions()),
            ])
            ->recordActions([
                Action::make('updateStatus')
                    ->label('تحديث الحالة')
                    ->icon('heroicon-o-user-circle')
                    ->authorize(fn (User $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->fillForm(fn (User $record): array => [
                        'status' => $record->status,
                        'reason' => $record->sellerProfile?->rejection_reason,
                    ])
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options(self::statusOptions())
                            ->required(),
                        Textarea::make('reason')
                            ->label('سبب الرفض أو الإيقاف')
                            ->rows(3),
                    ])
                    ->action(fn (User $record, array $data) => app(SellerService::class)->updateStatus(
                        $record,
                        UserStatus::from($data['status']),
                        $data['reason'] ?? null,
                    ))
                    ->successNotificationTitle('تم تحديث حالة البائع'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function statusOptions(): array
    {
        return [
            UserStatus::Pending->value => 'قيد المراجعة',
            UserStatus::Active->value => 'نشط',
            UserStatus::Suspended->value => 'موقوف',
            UserStatus::Rejected->value => 'مرفوض',
        ];
    }
}
