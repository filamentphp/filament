<a
    href="{{ url('/') }}"
    rel="home"
    class="p-4 flex items-center space-x-3 group"
    target="_blank"
    rel="noopener noreferrer"
>
    <div class="flex-shrink-0 rounded w-8 h-8 bg-red-800 text-white flex items-center justify-center transition-colors duration-200 group-hover:bg-red-700">
        <span class="font-bold italic">{{ substr(config('app.name'), 0, 1) }}</span>
    </div>

    <span class="text-sm leading-tight font-bold transition-colors duration-200 group-hover:text-white">{{ config('app.name') }}</span>
</a>
