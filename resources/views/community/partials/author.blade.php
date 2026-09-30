<span class="author-name">
    <i data-lucide="{{ $user->isSeller() ? 'store' : ($user->isAdmin() ? 'shield-check' : 'user-round') }}"></i>
    @if($user->isSeller())
        <a href="{{ route('sellers.show', $user) }}">{{ $user->public_name }}</a>
        <span class="author-badge author-badge-seller"><i data-lucide="badge-check"></i>{{ __('ui.community.seller_badge') }}</span>
    @else
        <strong>{{ $user->public_name }}</strong>
        @if($user->isAdmin())<span class="author-badge author-badge-admin">{{ __('ui.community.admin_badge') }}</span>@endif
    @endif
    @if($post->user_id === $user->id)<span class="author-badge">{{ __('ui.community.owner_badge') }}</span>@endif
</span>
