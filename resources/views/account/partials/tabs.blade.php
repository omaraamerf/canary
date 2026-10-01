{{-- Sections of the personal account; the settings tab is the edit page itself. --}}
@php
    $tabMeta = [
        'posts' => ['icon' => 'message-circle-question', 'label' => __('ui.account.tab_posts')],
        'replies' => ['icon' => 'message-square-reply', 'label' => __('ui.account.tab_replies')],
        'orders' => ['icon' => 'package', 'label' => __('ui.account.tab_orders')],
    ];
@endphp
<nav class="account-tabs" aria-label="{{ __('ui.account.sections') }}">
    @foreach($tabs as $key => $count)
        @php $isCurrent = $current === $key; @endphp
        <a @class(['account-tab', 'is-active' => $isCurrent]) href="{{ route('account.show', ['tab' => $key]) }}" @if($isCurrent) aria-current="page" @endif>
            <x-dynamic-component :component="'lucide-'.$tabMeta[$key]['icon']" />
            <span>{{ $tabMeta[$key]['label'] }}</span>
            <span class="account-tab-count">{{ $count }}</span>
        </a>
    @endforeach
    <a @class(['account-tab', 'is-active' => $current === 'settings']) href="{{ route('account.edit') }}" @if($current === 'settings') aria-current="page" @endif>
        <x-lucide-settings /><span>{{ __('ui.account.tab_settings') }}</span>
    </a>
</nav>
