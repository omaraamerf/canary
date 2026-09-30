<?php

namespace App\Filament\Shared\Actions;

use App\Enums\OrderStatus;
use App\Enums\Permission;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class UpdateOrderStatusAction
{
    public static function make(): Action
    {
        return Action::make('updateStatus')
            ->label(__('تحديث الحالة'))
            ->icon('heroicon-o-arrow-path')
            ->authorize(fn (Order $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->fillForm(fn (Order $record): array => [
                'status' => $record->status,
                'note' => null,
            ])
            ->schema([
                Select::make('status')
                    ->label(__('الحالة'))
                    ->options(self::statusOptions())
                    ->required(),
                Textarea::make('note')
                    ->label(__('ملاحظة'))
                    ->rows(3),
            ])
            ->action(function (Order $record, array $data): void {
                $actor = auth()->user();
                abort_unless($actor !== null, 403);

                app(OrderService::class)->updateStatus(
                    order: $record,
                    newStatus: OrderStatus::from($data['status']),
                    note: $data['note'] ?? null,
                    actor: $actor,
                    sellerId: $actor->can(Permission::UpdateAnyOrder->value) ? null : $actor->id,
                );
            })
            ->successNotificationTitle(__('تم تحديث حالة الطلب'));
    }

    public static function statusOptions(): array
    {
        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status): array => [$status->value => $status->label()])
            ->all();
    }
}
