<x-filament-widgets::widget>
    <x-filament::section :heading="$heading" icon="lucide-zap">
        <div class="panel-actions">
            @foreach($actions as $action)
                <a class="panel-action" href="{{ $action['url'] }}">@svg($action['icon'])<span>{{ $action['label'] }}</span></a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
