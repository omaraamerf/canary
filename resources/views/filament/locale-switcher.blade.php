<div class="mt-6 text-center">
    <a class="text-sm font-medium text-primary-600 hover:text-primary-500"
       href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}">
        {{ app()->isLocale('ar') ? 'English' : 'العربية' }}
    </a>
</div>
