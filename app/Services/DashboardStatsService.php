<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Bird;
use App\Models\Order;
use App\Models\SiteVisit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * @return array{users: int, sellers: int, sales: int, visits: int, unique_visitors: int}
     */
    public function summary(): array
    {
        return [
            'users' => User::query()->count(),
            'sellers' => User::role(UserRole::Seller->value)->count(),
            'sales' => Order::query()->where('status', OrderStatus::Delivered->value)->count(),
            'visits' => (int) SiteVisit::query()->sum('page_views'),
            'unique_visitors' => (int) SiteVisit::query()->sum('unique_visitors'),
        ];
    }

    /**
     * What is waiting on the admin team.
     *
     * @return array{new_orders: int, pending_birds: int, pending_sellers: int}
     */
    public function attention(): array
    {
        return [
            'new_orders' => Order::query()->where('status', OrderStatus::Pending->value)->count(),
            'pending_birds' => Bird::query()->where('approval_status', ApprovalStatus::Pending->value)->count(),
            'pending_sellers' => User::role(UserRole::Seller->value)->where('status', UserStatus::Pending->value)->count(),
        ];
    }

    /**
     * Page views and unique visitors per day, oldest first, with quiet days as zero.
     *
     * @return array{labels: list<string>, views: list<int>, visitors: list<int>}
     */
    public function dailyVisits(int $days = 30): array
    {
        $rows = SiteVisit::query()
            ->where('visited_on', '>=', now()->subDays($days - 1)->toDateString())
            ->get()
            ->keyBy(fn (SiteVisit $visit): string => $visit->visited_on->toDateString());

        $series = ['labels' => [], 'views' => [], 'visitors' => []];

        foreach ($this->days($days) as $day) {
            $series['labels'][] = $day->translatedFormat('j/n');
            $series['views'][] = (int) $rows->get($day->toDateString())?->page_views;
            $series['visitors'][] = (int) $rows->get($day->toDateString())?->unique_visitors;
        }

        return $series;
    }

    /**
     * Orders received per day, oldest first, with quiet days as zero.
     *
     * @return array{labels: list<string>, orders: list<int>}
     */
    public function dailyOrders(int $days = 30): array
    {
        $counts = Order::query()
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = ['labels' => [], 'orders' => []];

        foreach ($this->days($days) as $day) {
            $series['labels'][] = $day->translatedFormat('j/n');
            $series['orders'][] = (int) ($counts[$day->toDateString()] ?? 0);
        }

        return $series;
    }

    private function days(int $days): CarbonPeriod
    {
        return CarbonPeriod::create(now()->subDays($days - 1)->startOfDay(), '1 day', now()->startOfDay());
    }
}
