<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostCategory;
use App\Enums\PostStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')->label(__('العنوان'))->columnSpanFull(),
            TextEntry::make('user.name')->label(__('الكاتب')),
            TextEntry::make('user.email')->label(__('البريد الإلكتروني')),
            TextEntry::make('category')
                ->label(__('النوع'))
                ->badge()
                ->formatStateUsing(fn (PostCategory $state): string => $state->label()),
            TextEntry::make('status')
                ->label(__('الحالة'))
                ->badge()
                ->color(fn (PostStatus $state): string => $state === PostStatus::Published ? 'success' : 'gray')
                ->formatStateUsing(fn (PostStatus $state): string => $state->label()),
            TextEntry::make('breed.name')->label(__('السلالة'))->placeholder('-'),
            TextEntry::make('created_at')->label(__('تاريخ النشر'))->dateTime(),
            TextEntry::make('body')->label(__('التفاصيل'))->columnSpanFull(),
        ]);
    }
}
