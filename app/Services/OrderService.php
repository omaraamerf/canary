<?php

namespace App\Services;

use App\Enums\BirdStatus;
use App\Enums\OrderStatus;
use App\Models\Bird;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(Bird $bird, array $data): Order
    {
        return DB::transaction(function () use ($bird, $data) {
            $lockedBird = Bird::query()->lockForUpdate()->findOrFail($bird->id);

            abort_if($lockedBird->status !== BirdStatus::Available->value, 422, __('هذا الطائر لم يعد متاحًا للحجز.'));

            $region = Region::findOrFail($data['buyer_region_id']);
            $order = $lockedBird->orders()->create([
                ...$data,
                'city' => $region->name,
                'reference' => $this->reference(),
                'status' => OrderStatus::Pending->value,
                'delivery_method' => $lockedBird->delivery_type,
                'price_snapshot' => $lockedBird->price,
                'currency_snapshot' => $lockedBird->currency,
            ]);

            $order->statusLogs()->create([
                'new_status' => OrderStatus::Pending->value,
                'note' => __('تم استلام طلب الحجز من الموقع.'),
            ]);

            return $order;
        });
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?string $note, User $actor, ?int $sellerId = null): void
    {
        DB::transaction(function () use ($order, $newStatus, $note, $actor, $sellerId) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($sellerId !== null) {
                abort_unless($lockedOrder->bird()->where('seller_id', $sellerId)->exists(), 403);
            }

            $bird = Bird::query()->lockForUpdate()->findOrFail($lockedOrder->bird_id);
            $oldStatus = OrderStatus::from($lockedOrder->status);

            if ($newStatus->reservesBird() && $bird->status !== BirdStatus::Available->value && $oldStatus !== OrderStatus::Confirmed) {
                throw ValidationException::withMessages(['status' => __('الطائر محجوز أو مباع بالفعل.')]);
            }

            if ($newStatus->reservesBird()) {
                $bird->update(['status' => BirdStatus::Reserved->value]);
                Order::query()
                    ->where('bird_id', $bird->id)
                    ->whereKeyNot($lockedOrder->id)
                    ->where('status', OrderStatus::Pending->value)
                    ->update(['status' => OrderStatus::Cancelled->value]);
            } elseif ($newStatus->completesSale()) {
                $bird->update(['status' => BirdStatus::Sold->value]);
            } elseif ($newStatus === OrderStatus::Cancelled && in_array($oldStatus, [
                OrderStatus::Confirmed,
                OrderStatus::Preparing,
                OrderStatus::OutForDelivery,
            ], true)) {
                $bird->update(['status' => BirdStatus::Available->value]);
            }

            $lockedOrder->update(['status' => $newStatus->value]);
            $lockedOrder->statusLogs()->create([
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'note' => $note,
                'changed_by' => $actor->id,
            ]);
        });
    }

    private function reference(): string
    {
        do {
            $reference = 'CNY-'.Str::upper(Str::random(7));
        } while (Order::where('reference', $reference)->exists());

        return $reference;
    }
}
