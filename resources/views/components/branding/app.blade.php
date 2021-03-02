<a
    href="{{ url('/') }}"
    rel="home"
    class="p-4 flex items-center space-x-3 group bg-gray-900"
    target="_blank"
    rel="noopener noreferrer"
>
    <div class="flex-shrink-0 rounded w-10 h-10 bg-primary-600 text-white flex items-center justify-center transition-colors duration-200 group-hover:bg-primary-700">
        <span class="font-bold italic text-lg">{{ substr(config('app.name'), 0, 1) }}</span>
    </div>

    <span class="leading-tight text-white font-bold transition-colors duration-200 group-hover:text-gray-100 text-lg">{{ config('app.name') }}</span>
</a>
