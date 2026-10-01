@extends('layouts.app')

@section('title', __('ui.tracking.title'))

@section('content')
<section class="order-page">
    <div class="container order-split">
        <div class="order-intro">
            <span class="success-icon"><x-lucide-package-search /></span>
            <span class="kicker">{{ __('ui.tracking.eyebrow') }}</span>
            <h1>{{ __('ui.tracking.enter') }}</h1>
            <p>{{ __('ui.tracking.help') }}</p>
            <ol class="order-hints">
                <li><x-lucide-hash />{{ __('ui.tracking.reference') }} <code dir="ltr">CNY-XXXXXXX</code></li>
                <li><x-lucide-phone />{{ __('ui.tracking.phone_placeholder') }}</li>
            </ol>
        </div>
        <div class="order-card">
            @if($errors->any())
                <div class="form-errors">{{ $errors->first() }}</div>
            @endif
            <form class="tracking-form" method="post" action="{{ route('orders.track.lookup') }}">
                @csrf
                <label>
                    {{ __('ui.tracking.reference') }}
                    <input name="reference" value="{{ old('reference', $reference) }}" placeholder="CNY-XXXXXXX" required dir="ltr" autocomplete="off">
                </label>
                <label>
                    {{ __('ui.common.phone') }}
                    <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('ui.tracking.phone_placeholder') }}" required dir="ltr" inputmode="tel" autocomplete="tel">
                </label>
                <x-ui.button type="submit" size="lg" block icon="search">{{ __('ui.tracking.show') }}</x-ui.button>
            </form>
        </div>
    </div>
</section>
@endsection
