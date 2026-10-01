{{--
    Sign-in and sign-up pages: the form card beside a brand panel that says what the account is for.
    The panel goes under the form on narrow screens, without its photo.
--}}
@props(['title', 'lead' => null, 'icon' => 'log-in', 'image' => '/images/birds/yellow-canary.jpg', 'wide' => false])
<section class="auth-page">
    <div @class(['container', 'auth-layout', 'auth-layout-wide' => $wide])>
        <div class="auth-card">
            <div class="auth-card-head">
                <span class="auth-icon"><x-dynamic-component :component="'lucide-'.$icon" /></span>
                <h1>{{ $title }}</h1>
                @if($lead)<p>{{ $lead }}</p>@endif
            </div>
            {{ $slot }}
        </div>
        <aside class="auth-aside">
            <img class="auth-aside-image" src="{{ $image }}" alt="" width="640" height="800" loading="lazy" decoding="async">
            <div class="auth-aside-body">{{ $aside }}</div>
        </aside>
    </div>
</section>
