{{-- variant: neutral | primary | success | warning | danger | info | inverse --}}
@props(['variant' => 'neutral', 'icon' => null])
<span {{ $attributes->class(['badge', 'badge-'.$variant => $variant !== 'neutral']) }}>@if($icon)<x-dynamic-component :component="'lucide-'.$icon" />@endif{{ $slot }}</span>
