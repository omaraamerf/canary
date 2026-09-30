<?php

namespace App\Services;

use App\Models\Bird;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LogicException;

class EntityLifecycleService
{
    public function __construct(private readonly BirdService $birds) {}

    public function delete(Model $entity): bool
    {
        if ($entity instanceof Bird) {
            return $this->birds->delete($entity);
        }

        return DB::transaction(fn (): bool => (bool) $entity->delete());
    }

    public function forceDelete(Model $entity): bool
    {
        if ($entity instanceof Bird) {
            return $this->birds->forceDelete($entity);
        }

        if (! method_exists($entity, 'forceDelete')) {
            throw new LogicException('The entity does not support force deletion.');
        }

        return DB::transaction(fn (): bool => (bool) $entity->forceDelete());
    }

    public function restore(Model $entity): bool
    {
        if ($entity instanceof Bird) {
            return $this->birds->restore($entity);
        }

        if (! method_exists($entity, 'restore')) {
            throw new LogicException('The entity does not support restoration.');
        }

        return DB::transaction(fn (): bool => (bool) $entity->restore());
    }

    public function deleteMany(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->delete($entity);
        }
    }

    public function forceDeleteMany(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->forceDelete($entity);
        }
    }

    public function restoreMany(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->restore($entity);
        }
    }
}
