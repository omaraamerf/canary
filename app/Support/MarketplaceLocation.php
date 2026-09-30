<?php

namespace App\Support;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Builder;

/**
 * The visitor's chosen marketplace location: a whole country, one of its regions, or everywhere.
 */
class MarketplaceLocation
{
    private const COUNTRY_KEY = 'marketplace_country_id';

    private const REGION_KEY = 'marketplace_region_id';

    /** @var array{region?: ?Region, country?: ?Country} */
    private array $resolved = [];

    public function __construct(private readonly Session $session) {}

    public function countryId(): ?int
    {
        return $this->session->get(self::COUNTRY_KEY) ?: null;
    }

    public function regionId(): ?int
    {
        return $this->session->get(self::REGION_KEY) ?: null;
    }

    public function region(): ?Region
    {
        return $this->regionId()
            ? $this->resolved['region'] ??= Region::with('country')->find($this->regionId())
            : null;
    }

    public function country(): ?Country
    {
        return $this->countryId()
            ? $this->resolved['country'] ??= Country::find($this->countryId())
            : null;
    }

    public function choose(?Country $country, ?Region $region): void
    {
        $this->resolved = [];
        $this->session->put(self::COUNTRY_KEY, $region?->country_id ?? $country?->id);
        $this->session->put(self::REGION_KEY, $region?->id);
        $this->session->put('marketplace_region_chosen', true);
    }

    public function label(): string
    {
        return $this->region()?->full_name ?? $this->country()?->localized_name ?? __('ui.layout.all_regions');
    }

    /**
     * Limit a bird query to a region, or to every region of a country.
     */
    public static function scope(Builder $birds, ?int $countryId, ?int $regionId): Builder
    {
        return $birds
            ->when($regionId, fn (Builder $query) => $query->where('region_id', $regionId))
            ->when(! $regionId && $countryId, fn (Builder $query) => $query->whereHas('region', fn (Builder $region) => $region->where('country_id', $countryId)));
    }
}
