@props([
    'countryName' => 'country_id',
    'regionName' => 'region_id',
    'country' => null,
    'region' => null,
    'required' => false,
    'showOptional' => true,
    'useOld' => true,
    'countryLabel' => __('ui.location.country'),
    'regionLabel' => __('ui.location.region'),
    'countryPlaceholder' => __('ui.location.choose_country'),
    'regionPlaceholder' => __('ui.location.choose_region'),
])
@php
    $locations = app(\App\Support\LocationOptions::class);
    $region = $useOld ? old($regionName, $region) : $region;
    $country = ($useOld ? old($countryName, $country) : $country) ?: $locations->countryOfRegion($region ? (int) $region : null);
    $selectedCountry = $locations->countries()->firstWhere('id', (int) $country);
@endphp
<div {{ $attributes->class('location-picker') }} data-location-picker>
    <label>
        <span>{{ $countryLabel }}@if($showOptional && ! $required) <small>{{ __('ui.auth.optional') }}</small>@endif</span>
        <select name="{{ $countryName }}" data-location-country>
            <option value="">{{ $countryPlaceholder }}</option>
            @foreach($locations->countries() as $option)
                <option value="{{ $option->id }}" @selected($selectedCountry?->id === $option->id)>{{ $option->localized_name }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span>{{ $regionLabel }}@if($showOptional && ! $required) <small>{{ __('ui.auth.optional') }}</small>@endif</span>
        <select name="{{ $regionName }}" data-location-region data-placeholder="{{ $regionPlaceholder }}" data-empty="{{ __('ui.location.choose_country_first') }}" @required($required) @disabled(! $selectedCountry)>
            <option value="">{{ $selectedCountry ? $regionPlaceholder : __('ui.location.choose_country_first') }}</option>
            @foreach($selectedCountry?->regions ?? [] as $option)
                <option value="{{ $option->id }}" @selected((string) $region === (string) $option->id)>{{ $option->name }}</option>
            @endforeach
        </select>
    </label>
</div>
