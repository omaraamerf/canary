{{-- A password input with a show/hide toggle; the toggle appears only once JavaScript can run it. --}}
@props(['name' => 'password', 'autocomplete' => 'current-password'])
<span class="password-field">
    <input type="password" name="{{ $name }}" autocomplete="{{ $autocomplete }}" dir="ltr" {{ $attributes }}>
    <button type="button" class="password-toggle" data-password-toggle aria-pressed="false" aria-label="{{ __('ui.auth.show_password') }}" title="{{ __('ui.auth.show_password') }}" hidden><x-lucide-eye class="icon-show" /><x-lucide-eye-off class="icon-hide" /></button>
</span>
