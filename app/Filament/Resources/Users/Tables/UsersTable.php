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
                TextColumn::make('name')->label(__('الاسم'))->searchable()->sortable()->visibleFrom('md'),
                TextColumn::make('sellerProfile.display_name')->label(__('اسم المتجر'))->searchable()->weight('semibold'),
                TextColumn::make('email')->label(__('البريد الإلكتروني'))->searchable()->visibleFrom('lg'),
                TextColumn::make('phone')->label(__('الهاتف'))->searchable()->visibleFrom('lg'),
                TextColumn::make('sellerProfile.region.name')->label(__('المنطقة'))->visibleFrom('lg'),
                TextColumn::make('birds_count')->label(__('عدد الطيور'))->numeric()->sortable()->visibleFrom('md'),
                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')->label(__('تاريخ التسجيل'))->date('j F Y')->sortable()->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(self::statusOptions()),
            ])
            ->recordActions([
                Action::make('updateStatus')
                    ->label(__('تحديث الحالة'))
                    ->icon('lucide-user-round-check')
                    ->authorize(fn (User $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->fillForm(fn (User $record): array => [
                        'status' => $record->status,
                        'reason' => $record->sellerProfile?->rejection_reason,
                    ])
                    ->schema([
                        Select::make('status')
                            ->label(__('الحالة'))
                            ->options(self::statusOptions())
                            ->required(),
                        Textarea::make('reason')
                            ->label(__('سبب الرفض أو الإيقاف'))
                            ->rows(3),
                    ])
                    ->action(fn (User $record, array $data) => app(SellerService::class)->updateStatus(
                        $record,
                        UserStatus::from($data['status']),
                        $data['reason'] ?? null,
                    ))
                    ->successNotificationTitle(__('تم تحديث حالة البائع')),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function statusOptions(): array
    {
        return [
            UserStatus::Pending->value => __('قيد المراجعة'),
            UserStatus::Active->value => __('نشط'),
            UserStatus::Suspended->value => __('موقوف'),
            UserStatus::Rejected->value => __('مرفوض'),
        ];
    }
}
