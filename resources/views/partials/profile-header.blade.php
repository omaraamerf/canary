{{-- Shared header for the private account page and the public member profile. --}}
<section class="profile-hero"><div class="container">
    <div class="profile-card">
        <x-avatar :user="$member" size="xl" />
        <div class="profile-card-body">
            <h1>
                {{ $member->public_name }}
                @if($member->isSeller())<span class="author-badge author-badge-seller"><x-lucide-badge-check />{{ __('ui.community.seller_badge') }}</span>@endif
                @if($member->isAdmin())<span class="author-badge author-badge-admin">{{ __('ui.community.admin_badge') }}</span>@endif
            </h1>
            <p class="profile-meta">
                @if($member->location_label)<span><x-lucide-map-pin />{{ $member->location_label }}</span>@endif
                <span><x-lucide-calendar-days />{{ __('ui.account.member_since', ['date' => $member->created_at->translatedFormat('F Y')]) }}</span>
            </p>
            @if($member->bio)<p class="profile-bio">{{ $member->bio }}</p>@elseif($isOwner)<p class="profile-bio profile-bio-empty">{{ __('ui.account.no_bio') }}</p>@endif
        </div>
        <div class="profile-card-actions">
            @if($isOwner)
                <a class="btn btn-primary" href="{{ route('account.edit') }}"><x-lucide-user-round-pen />{{ __('ui.account.edit') }}</a>
                <a class="btn btn-outline" href="{{ route('members.show', $member) }}"><x-lucide-eye />{{ __('ui.account.public_profile') }}</a>
                @if($member->isSeller())<a class="btn btn-outline" href="{{ url('/seller') }}"><x-lucide-layout-dashboard />{{ __('ui.nav.seller_panel') }}</a>@endif
            @elseif($member->isSeller() && $member->status === 'active')
                <a class="btn btn-outline" href="{{ route('sellers.show', $member) }}"><x-lucide-store />{{ __('ui.account.visit_store') }}</a>
            @endif
        </div>
    </div>
    <dl class="profile-stats">
        <div><dt>{{ __('ui.account.stats_posts') }}</dt><dd>{{ $stats['posts'] }}</dd></div>
        <div><dt>{{ __('ui.account.stats_replies') }}</dt><dd>{{ $stats['replies'] }}</dd></div>
        <div><dt>{{ __('ui.account.stats_solutions') }}</dt><dd>{{ $stats['solutions'] }}</dd></div>
    </dl>
</div></section>
