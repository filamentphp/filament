<a
    href="{{ url('/') }}"
    rel="home"
    class="p-4 flex items-center space-x-3 group"
    target="_blank"
    rel="noopener noreferrer"
>
    <div class="flex-shrink-0 rounded w-8 h-8 bg-primary-600 text-white flex items-center justify-center transition-colors duration-200 group-hover:bg-primary-500">
        <span class="font-bold italic">{{ substr(config('app.name'), 0, 1) }}</span>
    </div>

    <span class="leading-tight font-medium transition-colors duration-200 group-hover:text-white">{{ config('app.name') }}</span>
</a>
