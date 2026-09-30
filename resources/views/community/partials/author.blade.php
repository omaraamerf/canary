<span class="author-name">
    <x-avatar :user="$user" size="sm" />
    <a href="{{ route('members.show', $user) }}">{{ $user->public_name }}</a>
    @if($user->isSeller())
        <span class="author-badge author-badge-seller"><i data-lucide="badge-check"></i>{{ __('ui.community.seller_badge') }}</span>
    @elseif($user->isAdmin())
        <span class="author-badge author-badge-admin">{{ __('ui.community.admin_badge') }}</span>
    @endif
    @if($post->user_id === $user->id)<span class="author-badge">{{ __('ui.community.owner_badge') }}</span>@endif
</span>
