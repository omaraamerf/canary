<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Enums\PostCategory;
use App\Enums\PostStatus;
use App\Filament\Shared\Actions\EntityActions;
use App\Models\Post;
use App\Services\CommunityService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('العنوان'))->searchable()->limit(60),
                TextColumn::make('user.name')->label(__('الكاتب'))->searchable(),
                TextColumn::make('category')
                    ->label(__('النوع'))
                    ->badge()
                    ->formatStateUsing(fn (PostCategory $state): string => $state->label()),
                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->color(fn (PostStatus $state): string => $state === PostStatus::Published ? 'success' : 'gray')
                    ->formatStateUsing(fn (PostStatus $state): string => $state->label()),
                TextColumn::make('comments_count')->label(__('الردود'))->sortable(),
                IconColumn::make('accepted_comment_id')
                    ->label(__('تم الحل'))
                    ->state(fn (Post $record): bool => $record->isSolved())
                    ->boolean(),
                TextColumn::make('created_at')->label(__('تاريخ النشر'))->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('النوع'))->options(PostCategory::options()),
                SelectFilter::make('status')->label(__('الحالة'))->options(PostStatus::options()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label(__('عرض')),
                self::toggleStatusAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    EntityActions::deleteBulk(),
                    EntityActions::forceDeleteBulk(),
                    EntityActions::restoreBulk(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function toggleStatusAction(): Action
    {
        return Action::make('toggleStatus')
            ->label(fn (Post $record): string => $record->status === PostStatus::Published ? __('إخفاء') : __('إظهار'))
            ->icon(fn (Post $record): string => $record->status === PostStatus::Published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
            ->color(fn (Post $record): string => $record->status === PostStatus::Published ? 'danger' : 'success')
            ->requiresConfirmation()
            ->authorize(fn (Post $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->action(fn (Post $record) => app(CommunityService::class)->setPostStatus(
                $record,
                $record->status === PostStatus::Published ? PostStatus::Hidden : PostStatus::Published,
            ))
            ->successNotificationTitle(__('تم تحديث حالة الاستفسار'));
    }

    public static function openOnSiteAction(): Action
    {
        return Action::make('openOnSite')
            ->label(__('فتح في الموقع'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->color('gray')
            ->url(fn (Post $record): string => route('community.show', $record), shouldOpenInNewTab: true)
            ->hidden(fn (Post $record): bool => $record->trashed());
    }
}
