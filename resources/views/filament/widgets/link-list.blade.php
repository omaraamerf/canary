{{-- A short list of links with a count each; a non-zero count is highlighted as due. --}}
<x-filament-widgets::widget>
    <x-filament::section :heading="$heading" :icon="$icon">
        <ul class="panel-list">
            @foreach($items as $item)
                <li>
                    <a href="{{ $item['url'] }}">
                        <span class="panel-list-icon">@svg($item['icon'])</span>
                        <span>{{ $item['label'] }}</span>
                        <span @class(['panel-list-count', 'is-due' => $item['count'] > 0])>{{ number_format($item['count']) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
