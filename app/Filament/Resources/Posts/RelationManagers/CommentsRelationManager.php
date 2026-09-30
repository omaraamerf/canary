<?php

namespace App\Filament\Resources\Posts\RelationManagers;

use App\Models\Comment;
use App\Services\CommunityService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('الردود'))
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('user.name')->label(__('الكاتب'))->searchable(),
                TextColumn::make('user.role')
                    ->label(__('النوع'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'seller' => __('بائع'),
                        'admin' => __('إدارة'),
                        default => __('عضو'),
                    }),
                TextColumn::make('body')->label(__('الرد'))->limit(100)->wrap(),
                IconColumn::make('is_hidden')->label(__('مخفي'))->boolean(),
                TextColumn::make('created_at')->label(__('التاريخ'))->dateTime()->sortable(),
            ])
            ->recordActions([
                Action::make('toggleHidden')
                    ->label(fn (Comment $record): string => $record->is_hidden ? __('إظهار') : __('إخفاء'))
                    ->icon(fn (Comment $record): string => $record->is_hidden ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn (Comment $record): string => $record->is_hidden ? 'success' : 'danger')
                    ->authorize(fn (Comment $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->action(fn (Comment $record) => app(CommunityService::class)->setCommentHidden($record, ! $record->is_hidden)),
                DeleteAction::make()
                    ->using(fn (Comment $record): bool => app(CommunityService::class)->deleteComment($record)),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
