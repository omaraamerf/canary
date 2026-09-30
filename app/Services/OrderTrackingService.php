<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Session\Store;
use Illuminate\Validation\ValidationException;

class OrderTrackingService
{
    public function findForCustomer(string $reference, string $phone): Order
    {
        $order = Order::query()
            ->where('reference', strtoupper(trim($reference)))
            ->first();

        if (! $order || ! hash_equals($this->normalizePhone($order->phone), $this->normalizePhone($phone))) {
            throw ValidationException::withMessages([
                'reference' => __('رقم الطلب أو رقم الهاتف غير صحيح.'),
            ]);
        }

        return $order;
    }

    public function authorizeSession(Store $session, Order $order): void
    {
        $session->put($this->sessionKey($order), true);
    }

    public function sessionCanTrack(Store $session, Order $order): bool
    {
        return (bool) $session->get($this->sessionKey($order), false);
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function sessionKey(Order $order): string
    {
        return 'tracked_orders.'.$order->getKey();
    }
}
