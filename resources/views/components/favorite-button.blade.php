{{--
    Save a bird to this browser's favourites (resources/js/favorites.js). Hidden until the
    script runs, since it has nothing to do without it. aria-pressed tells whether it is saved.
--}}
@props(['bird', 'withLabel' => false])
<button type="button" {{ $attributes->class(['fav-toggle', 'has-label' => $withLabel]) }} data-favorite="{{ $bird->id }}" aria-pressed="false"
    @unless($withLabel) aria-label="{{ __('ui.favorites.toggle', ['title' => $bird->title]) }}" title="{{ __('ui.favorites.save') }}" @endunless
    data-added-message="{{ __('ui.favorites.added') }}" data-removed-message="{{ __('ui.favorites.removed') }}" hidden>
    <x-lucide-heart />@if($withLabel)<span>{{ __('ui.favorites.save') }}</span>@endif
</button>
