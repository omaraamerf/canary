{{-- variant: primary | dark | outline | ghost | danger — size: sm | lg — renders <a> when href is given. --}}
@props([
    'variant' => 'primary',
    'size' => null,
    'icon' => null,
    'iconEnd' => null,
    'href' => null,
    'type' => 'button',
    'block' => false,
])
@php
    $classes = ['btn', 'btn-'.$variant, 'btn-sm' => $size === 'sm', 'btn-large' => $size === 'lg', 'btn-block' => $block];
@endphp
@if($href)
<a href="{{ $href }}" {{ $attributes->class($classes) }}>
@else
<button type="{{ $type }}" {{ $attributes->class($classes) }}>
@endif
    @if($icon)<x-dynamic-component :component="'lucide-'.$icon" />@endif
    {{ $slot }}
    @if($iconEnd)<x-dynamic-component :component="'lucide-'.$iconEnd" />@endif
@if($href)
</a>
@else
</button>
@endif
