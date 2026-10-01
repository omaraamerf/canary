@props([
    'name' => 'images',
    'max' => 4,
    'multiple' => true,
    'label' => __('ui.uploads.add_images'),
    'hint' => null,
])
<div {{ $attributes->class('image-picker') }} data-image-picker data-max="{{ $multiple ? $max : 1 }}" data-max-message="{{ __('ui.uploads.max_reached', ['max' => $multiple ? $max : 1]) }}">
    <label class="image-picker-drop" data-image-drop>
        <input type="file" name="{{ $multiple ? $name.'[]' : $name }}" accept="image/*" @if($multiple) multiple @endif data-image-input>
        <span class="image-picker-icon"><x-lucide-image-plus /></span>
        <strong>{{ $label }}</strong>
        <small>{{ $hint ?? ($multiple ? __('ui.uploads.hint_many', ['max' => $max]) : __('ui.uploads.hint_one')) }}</small>
    </label>
    <div class="image-picker-previews" data-image-previews aria-live="polite"></div>
    <p class="image-picker-status" data-image-status hidden></p>
</div>
