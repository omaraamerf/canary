<?php

namespace App\Support;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;

/**
 * Active countries with their active regions, loaded once per request
 * and shared by every country → region picker on the page.
 */
class LocationOptions
{
    private ?Collection $countries = null;

    public function countries(): Collection
    {
        return $this->countries ??= Country::query()
            ->active()
            ->with(['regions' => fn ($query) => $query->where('active', true)->orderBy('sort_order')->orderBy('name')])
            ->get();
    }

    /**
     * @return array<int, array{id: int, name: string, regions: array<int, array{id: int, name: string}>}>
     */
    public function toArray(): array
    {
        return $this->countries()->map(fn (Country $country) => [
            'id' => $country->id,
            'name' => $country->localized_name,
            'regions' => $country->regions->map(fn (Region $region) => ['id' => $region->id, 'name' => $region->name])->values()->all(),
        ])->values()->all();
    }

    /**
     * Fill the country from the chosen region so both always agree.
     */
    public function normalize(array $data, string $countryKey = 'country_id', string $regionKey = 'region_id'): array
    {
        if (! empty($data[$regionKey])) {
            $data[$countryKey] = $this->countryOfRegion((int) $data[$regionKey]);
        }

        return $data;
    }

    public function countryOfRegion(?int $regionId): ?int
    {
        if (! $regionId) {
            return null;
        }

        foreach ($this->countries() as $country) {
            if ($country->regions->contains('id', $regionId)) {
                return $country->id;
            }
        }

        return Region::query()->whereKey($regionId)->value('country_id');
    }
}
