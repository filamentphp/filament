@props([
    'breadcrumbs' => [],
])

<nav {{ $attributes->class(['fi-breadcrumbs']) }}>
    <ul class="flex flex-wrap items-center gap-x-2">
        @foreach ($breadcrumbs as $url => $label)
            <li class="flex gap-x-2">
                @if (! $loop->first)
                    <x-filament::icon
                        alias="breadcrumbs.separator"
                        name="heroicon-m-chevron-right"
                        class="h-5 w-5 text-gray-400 dark:text-gray-500"
                    />
                @endif

                <a
                    href="{{ is_int($url) ? '#' : $url }}"
                    class="text-sm font-medium text-gray-500 outline-none transition duration-75 hover:text-gray-700 focus:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 dark:focus:text-gray-200"
                >
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
