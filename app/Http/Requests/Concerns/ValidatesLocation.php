<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesLocation
{
    /**
     * An optional country followed by a region that must belong to it.
     */
    protected function locationRules(bool $regionRequired = false, string $countryKey = 'country_id', string $regionKey = 'region_id'): array
    {
        return [
            $countryKey => ['nullable', 'integer', Rule::exists('countries', 'id')->where('active', true)],
            $regionKey => [
                $regionRequired ? 'required' : 'nullable',
                'integer',
                Rule::exists('regions', 'id')
                    ->where('active', true)
                    ->where(fn ($query) => $this->filled($countryKey) ? $query->where('country_id', $this->input($countryKey)) : $query),
            ],
        ];
    }
}
