<?php

namespace App\Filament\Shared\Tables;

use App\Enums\Currency;
use App\Enums\OrderStatus;
use App\Models\Bird;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

/** Columns that several panel tables share, so a bird or an order reads the same everywhere. */
class Columns
{
    public static function birdImage(string $name = 'primary_image', ?callable $bird = null): ImageColumn
    {
        $bird ??= fn ($record): ?Bird => $record;

        return ImageColumn::make($name)
            ->label(__('الصورة'))
            // An absolute URL: Filament would otherwise read "/images/…" as a path on the storage disk.
            ->state(fn ($record): ?string => ($image = $bird($record)?->primary_image) ? url($image) : null)
            ->square()
            ->imageSize(48)
            ->extraImgAttributes(['alt' => '', 'loading' => 'lazy']);
    }

    public static function birdStatus(): TextColumn
    {
        return TextColumn::make('status')
            ->label(__('الحالة'))
            ->badge()
            ->formatStateUsing(fn (string $state): string => match ($state) {
                'available' => __('متاح'),
                'reserved' => __('محجوز'),
                'sold' => __('مباع'),
                default => $state,
            })
            ->color(fn (string $state): string => match ($state) {
                'available' => 'success',
                'reserved' => 'warning',
                default => 'gray',
            });
    }

    public static function approvalStatus(): TextColumn
    {
        return TextColumn::make('approval_status')
            ->label(__('المراجعة'))
            ->badge()
            ->formatStateUsing(fn (string $state): string => match ($state) {
                'pending' => __('بانتظار المراجعة'),
                'approved' => __('منشور'),
                'rejected' => __('مرفوض'),
                default => $state,
            })
            ->color(fn (string $state): string => match ($state) {
                'pending' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'gray',
            });
    }

    public static function orderStatus(): TextColumn
    {
        return TextColumn::make('status')
            ->label(__('الحالة'))
            ->badge()
            ->formatStateUsing(fn (string $state): string => OrderStatus::tryFrom($state)?->label() ?? $state)
            ->color(fn (string $state): string => OrderStatus::tryFrom($state)?->badge() ?? 'gray')
            ->icon(fn (string $state): ?string => ($status = OrderStatus::tryFrom($state)) ? 'lucide-'.$status->icon() : null);
    }

    /** A price in whole units with its currency name, as on the public site. */
    public static function price(string $name, string $currencyAttribute): TextColumn
    {
        return TextColumn::make($name)
            ->label(__('السعر'))
            ->formatStateUsing(fn ($state, $record): string => number_format((float) $state).' '.Currency::labelFor($record->{$currencyAttribute}));
    }
}
