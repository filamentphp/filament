@props([
    'breadcrumbs' => [],
    'title',
])

<header class="p-4 md:py-6 md:px-8 flex justify-between items-center space-x-4">
    <div class="flex items-center">
        <button
            type="button"
            aria-controls="banner"
            @click.prevent="headerIsOpen = true"
            :aria-expanded="headerIsOpen"
            class="md:hidden text-gray-500 hover:text-gray-700 transition-colors duration-200 mr-4"
        >
            <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
        </button>

        <div class="space-y-1">
            @if (count($breadcrumbs))
                <p class="font-mono text-sm text-gray-700">
                    @foreach ($breadcrumbs as $url => $label)
                        <a href="{{ $url }}" class="hover:underline">{{ $label }}</a>

                        <span>/</span>
                    @endforeach
                </p>
            @endif

            <h1 class="text-2xl md:text-3xl leading-tight text-primary-700">{{ $title }}</h1>
        </div>
    </div>

    {{ $actions ?? null }}
</header>
