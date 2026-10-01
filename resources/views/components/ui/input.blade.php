{{--
    A labelled form control with hint and validation error wired for screen readers.
    type: any <input> type, or "textarea" / "select" (pass options as [value => label]).
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'hint' => null,
    'required' => false,
    'optional' => false,
    'id' => null,
])
@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id ??= 'field-'.str_replace('.', '-', $key);
    $error = $errors->first($key);
    $current = old($key, $value);
    $describedBy = collect([$hint ? $id.'-hint' : null, $error ? $id.'-error' : null])->filter()->implode(' ');
    $control = $attributes->merge(array_filter([
        'id' => $id,
        'name' => $name,
        'placeholder' => $placeholder,
        'aria-invalid' => $error ? 'true' : null,
        'aria-describedby' => $describedBy ?: null,
    ]));
@endphp
<div class="field">
    <label class="field-label" for="{{ $id }}">
        {{ $label }}
        @if($required)<span class="field-required" aria-hidden="true">*</span>@elseif($optional)<span class="field-optional">{{ __('ui.auth.optional') }}</span>@endif
    </label>
    @if($type === 'textarea')
        <textarea {{ $control }} @required($required)>{{ $current }}</textarea>
    @elseif($type === 'select')
        <select {{ $control }} @required($required)>
            @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" {{ $control }} @if($type !== 'password') value="{{ $current }}" @endif @required($required)>
    @endif
    @if($hint)<p class="field-hint" id="{{ $id }}-hint">{{ $hint }}</p>@endif
    @if($error)<p class="field-error" id="{{ $id }}-error"><x-lucide-circle-alert />{{ $error }}</p>@endif
</div>
