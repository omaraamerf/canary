{{-- An icon-only button; the label is required because it is the accessible name. --}}
@props([
    'icon',
    'label',
    'href' => null,
    'type' => 'button',
    'variant' => null,
])
@php
    $classes = ['icon-btn', 'icon-btn-ghost' => $variant === 'ghost'];
@endphp
@if($href)
<a href="{{ $href }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->class($classes) }}><x-dynamic-component :component="'lucide-'.$icon" /></a>
@else
<button type="{{ $type }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->class($classes) }}><x-dynamic-component :component="'lucide-'.$icon" /></button>
@endif
