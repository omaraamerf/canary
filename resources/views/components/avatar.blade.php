@props(['user', 'size' => null])
@if($user->avatar_url)
    <img {{ $attributes->class(['avatar', 'avatar-'.$size => $size]) }} src="{{ $user->avatar_url }}" alt="{{ $user->public_name }}" loading="lazy">
@else
    <span {{ $attributes->class(['avatar', 'avatar-'.$size => $size]) }} aria-hidden="true">{{ $user->initial }}</span>
@endif
