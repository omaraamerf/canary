{{-- A pill used for filters and quick choices: a link when href is given, otherwise a toggle button. --}}
@props([
    'href' => null,
    'active' => false,
    'icon' => null,
    'iconEnd' => null,
])
@php
    $classes = ['chip', 'is-active' => $active];
@endphp
@if($href)
<a href="{{ $href }}" @if($active) aria-current="true" @endif {{ $attributes->class($classes) }}>
@else
<button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}" {{ $attributes->class($classes) }}>
@endif
    @if($icon)<x-dynamic-component :component="'lucide-'.$icon" />@endif
    <span>{{ $slot }}</span>
    @if($iconEnd)<x-dynamic-component :component="'lucide-'.$iconEnd" class="chevron" />@endif
@if($href)
</a>
@else
</button>
@endif
