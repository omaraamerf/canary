{{-- Guide side column: section navigation and a nudge to ask the community. --}}
<aside class="guide-aside">
    <nav class="side-card" aria-label="{{ __('ui.guide.all_categories') }}">
        <h2>{{ __('ui.guide.all_categories') }}</h2>
        <ul class="side-nav">
            @foreach($categories as $item)
                <li><a @class(['is-active' => isset($current) && $current->is($item)]) href="{{ route('guide.category', $item) }}" @if(isset($current) && $current->is($item)) aria-current="page" @endif><x-dynamic-component :component="'lucide-'.$item->icon" /><span>{{ $item->name }}</span><b>{{ $item->published_articles_count }}</b></a></li>
            @endforeach
        </ul>
    </nav>
    @if($communityEnabled)
        <div class="side-card side-card-cta">
            <x-lucide-messages-square />
            <h2>{{ __('ui.guide.ask_title') }}</h2>
            <p>{{ __('ui.guide.ask_text') }}</p>
            <x-ui.button variant="dark" size="sm" :href="route('community.create')">{{ __('ui.guide.ask_button') }}</x-ui.button>
        </div>
    @endif
</aside>
