<a
    href="{{ url('/') }}"
    rel="home"
    class="flex items-center p-4 space-x-3 rtl:space-x-reverse group"
    target="_blank"
    rel="noopener noreferrer"
>
    <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-white transition-colors duration-200 rounded bg-primary-600 group-hover:bg-primary-700">
        <span class="text-lg italic font-bold">{{ substr(config('app.name'), 0, 1) }}</span>
    </div>

    <span class="text-lg font-bold leading-tight text-white transition-colors duration-200 group-hover:text-gray-100">{{ config('app.name') }}</span>
</a>
