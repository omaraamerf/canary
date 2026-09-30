<?php

namespace App\Filament\Shared\Schemas;

use App\Filament\Shared\Actions\UpdateOrderStatusAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema, bool $protectBuyerPhone = false): Schema
    {
        return $schema->components([
            TextEntry::make('reference')->label(__('رقم الطلب')),
            TextEntry::make('bird.title')->label(__('الطائر')),
            TextEntry::make('buyer_name')->label(__('اسم المشتري')),
            TextEntry::make('phone')
                ->label(__('الهاتف'))
                ->formatStateUsing(fn (string $state, $record): string => ! $protectBuyerPhone || $record->buyerPhoneCanBeRevealed()
                    ? $state
                    : __('يظهر بعد تحويل الطلب إلى قيد التجهيز')),
            TextEntry::make('city')->label(__('المدينة')),
            TextEntry::make('buyerRegion.name')->label(__('المنطقة'))->placeholder('-'),
            TextEntry::make('delivery_method')
                ->label(__('طريقة التسليم'))
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'pickup' => __('استلام شخصي'),
                    'delivery' => __('توصيل'),
                    'agreement' => __('بالاتفاق'),
                    default => $state,
                }),
            TextEntry::make('notes')->label(__('الملاحظات'))->placeholder('-')->columnSpanFull(),
            TextEntry::make('status')
                ->label(__('الحالة'))
                ->badge()
                ->formatStateUsing(fn (string $state): string => UpdateOrderStatusAction::statusOptions()[$state] ?? $state),
            TextEntry::make('price_snapshot')
                ->label(__('السعر'))
                ->formatStateUsing(fn ($state, $record): string => number_format((float) $state, 2).' '.$record->currency_snapshot),
            TextEntry::make('created_at')->label(__('تاريخ الطلب'))->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->label(__('آخر تحديث'))->dateTime()->placeholder('-'),
        ]);
    }
}
