@props(['user', 'size' => null])
@if($user->avatar_url)
    <x-img {{ $attributes->class(['avatar', 'avatar-'.$size => $size]) }} :src="$user->avatar_url" :width="96" :alt="$user->public_name" loading="lazy" />
@else
    <span {{ $attributes->class(['avatar', 'avatar-'.$size => $size]) }} aria-hidden="true">{{ $user->initial }}</span>
@endif
