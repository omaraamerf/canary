{{-- Flash messages from redirects (->with('success', …) / ->with('error', …)). --}}
@php
    $toasts = array_filter(['success' => session('success'), 'danger' => session('error')]);
@endphp
@if($toasts)
    <div class="toast-region" role="status" aria-live="polite">
        @foreach($toasts as $type => $message)
            <div @class(['toast', 'toast-danger' => $type === 'danger']) data-toast>
                <x-dynamic-component :component="$type === 'danger' ? 'lucide-circle-alert' : 'lucide-circle-check'" />
                <p>{{ $message }}</p>
                <x-ui.icon-button icon="x" :label="__('ui.layout.close')" variant="ghost" data-toast-close />
            </div>
        @endforeach
    </div>
@endif
