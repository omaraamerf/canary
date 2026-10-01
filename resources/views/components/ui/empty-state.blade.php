@props(['icon' => 'inbox', 'title', 'text' => null])
<div {{ $attributes->class('empty-state') }}>
    <x-dynamic-component :component="'lucide-'.$icon" />
    <h2>{{ $title }}</h2>
    @if($text)<p>{{ $text }}</p>@endif
    {{ $slot }}
</div>
