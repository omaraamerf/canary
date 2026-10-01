@extends('layouts.app')

@section('title', __('ui.tracking.title'))

@section('content')
<section class="success-page">
    <div class="success-panel tracking-panel">
        <span class="success-icon"><x-lucide-search /></span>
        <span class="kicker">{{ __('ui.tracking.eyebrow') }}</span>
        <h1>{{ __('ui.tracking.enter') }}</h1>
        <p>{{ __('ui.tracking.help') }}</p>

        @if($errors->any())
            <div class="form-errors">{{ $errors->first() }}</div>
        @endif

        <form class="tracking-form" method="post" action="{{ route('orders.track.lookup') }}">
            @csrf
            <label>
                {{ __('ui.tracking.reference') }}
                <input name="reference" value="{{ old('reference', $reference) }}" placeholder="CNY-XXXXXXX" required dir="ltr">
            </label>
            <label>
                {{ __('ui.common.phone') }}
                <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('ui.tracking.phone_placeholder') }}" required dir="ltr">
            </label>
            <button class="btn btn-primary btn-large" type="submit">
                <x-lucide-search /> {{ __('ui.tracking.show') }}
            </button>
        </form>
    </div>
</section>
@endsection
