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
            TextEntry::make('reference')->label('رقم الطلب'),
            TextEntry::make('bird.title')->label('الطائر'),
            TextEntry::make('buyer_name')->label('اسم المشتري'),
            TextEntry::make('phone')
                ->label('الهاتف')
                ->formatStateUsing(fn (string $state, $record): string => ! $protectBuyerPhone || $record->buyerPhoneCanBeRevealed()
                    ? $state
                    : 'يظهر بعد تحويل الطلب إلى قيد التجهيز'),
            TextEntry::make('city')->label('المدينة'),
            TextEntry::make('buyerRegion.name')->label('المنطقة')->placeholder('-'),
            TextEntry::make('delivery_method')
                ->label('طريقة التسليم')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'pickup' => 'استلام شخصي',
                    'delivery' => 'توصيل',
                    'agreement' => 'بالاتفاق',
                    default => $state,
                }),
            TextEntry::make('notes')->label('الملاحظات')->placeholder('-')->columnSpanFull(),
            TextEntry::make('status')
                ->label('الحالة')
                ->badge()
                ->formatStateUsing(fn (string $state): string => UpdateOrderStatusAction::statusOptions()[$state] ?? $state),
            TextEntry::make('price_snapshot')
                ->label('السعر')
                ->formatStateUsing(fn ($state, $record): string => number_format((float) $state, 2).' '.$record->currency_snapshot),
            TextEntry::make('created_at')->label('تاريخ الطلب')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->label('آخر تحديث')->dateTime()->placeholder('-'),
        ]);
    }
}
