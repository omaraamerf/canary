<?php

namespace App\Filament\Shared\Actions;

use App\Services\EntityLifecycleService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class EntityActions
{
    public static function delete(): DeleteAction
    {
        return DeleteAction::make()
            ->using(fn (Model $record): bool => app(EntityLifecycleService::class)->delete($record));
    }

    public static function deleteBulk(): DeleteBulkAction
    {
        return DeleteBulkAction::make()
            ->using(function (DeleteBulkAction $action, $records): void {
                self::processBulk($action, $records, 'delete');
            });
    }

    public static function forceDelete(): ForceDeleteAction
    {
        return ForceDeleteAction::make()
            ->using(fn (Model $record): bool => app(EntityLifecycleService::class)->forceDelete($record));
    }

    public static function forceDeleteBulk(): ForceDeleteBulkAction
    {
        return ForceDeleteBulkAction::make()
            ->using(function (ForceDeleteBulkAction $action, $records): void {
                self::processBulk($action, $records, 'forceDelete');
            });
    }

    public static function restore(): RestoreAction
    {
        return RestoreAction::make()
            ->using(fn (Model $record): bool => app(EntityLifecycleService::class)->restore($record));
    }

    public static function restoreBulk(): RestoreBulkAction
    {
        return RestoreBulkAction::make()
            ->using(function (RestoreBulkAction $action, $records): void {
                self::processBulk($action, $records, 'restore');
            });
    }

    private static function processBulk($action, iterable $records, string $operation): void
    {
        $isFirstException = true;

        foreach ($records as $record) {
            try {
                app(EntityLifecycleService::class)->{$operation}($record)
                    || $action->reportBulkProcessingFailure();
            } catch (Throwable $exception) {
                $action->reportBulkProcessingFailure();

                if ($isFirstException) {
                    report($exception);
                    $isFirstException = false;
                }
            }
        }
    }
}
