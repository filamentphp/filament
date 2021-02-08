@props([
    'title',
])

<header class="p-4 md:p-6 flex justify-between items-center space-x-4">
    <div class="flex items-center">
        <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = true"
                :aria-expanded="headerIsOpen"
                class="md:hidden text-blue-600 hover:text-blue-800 transition-colors duration-200 mr-4">
            <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
        </button>

        <h1 class="text-2xl leading-tight text-red-700">{{ $title }}</h1>
    </div>

    {{ $actions ?? null }}
</header>
