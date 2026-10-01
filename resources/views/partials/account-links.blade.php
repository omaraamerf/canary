{{-- Signed-in account links, shared by the header account menu and the mobile menu sheet. --}}
<div class="menu-head"><strong>{{ $user->public_name }}</strong><small dir="ltr">{{ $user->email }}</small></div>
<a @class(['menu-item', 'is-active' => request()->routeIs('account.show')]) href="{{ route('account.show') }}"><x-lucide-user-round />{{ __('ui.account.my_account') }}</a>
<a @class(['menu-item', 'is-active' => request()->routeIs('account.edit')]) href="{{ route('account.edit') }}"><x-lucide-user-round-pen />{{ __('ui.account.edit') }}</a>
@if($user->isSeller())<a class="menu-item" href="{{ url('/seller') }}"><x-lucide-layout-dashboard />{{ __('ui.nav.seller_panel') }}</a>@endif
@if($user->isAdmin())<a class="menu-item" href="{{ url('/admin') }}"><x-lucide-shield-check />{{ __('ui.nav.admin_panel') }}</a>@endif
@if($communityEnabled)<a class="menu-item" href="{{ route('community.create') }}"><x-lucide-message-circle-plus />{{ __('ui.community.new_post') }}</a>@endif
<hr class="menu-divider">
<form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="menu-item menu-item-danger"><x-lucide-log-out />{{ __('ui.nav.logout') }}</button></form>
