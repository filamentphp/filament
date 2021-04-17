@props([
    'breadcrumbs' => [],
    'title',
])

<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __($title) }}</h2>
            <div>
                <div class="flex items-center">
                    <button
                            type="button"
                            aria-controls="banner"
                            @click.prevent="headerIsOpen = true"
                            :aria-expanded="headerIsOpen"
                            class="mr-4 text-gray-500 transition-colors duration-200 md:hidden hover:text-gray-700"
                    >
                        <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
                    </button>

                    <div class="space-y-1">
                        @if (count($breadcrumbs))
                            <p class="-mt-2 font-mono text-sm text-gray-500">
                                @foreach ($breadcrumbs as $url => $label)
                                    <a href="{{ $url }}" class="hover:underline">{{ __($label) }}</a>

                                    <span>/</span>
                                @endforeach
                            </p>
                        @endif
                    </div>
                </div>

                {{ $actions ?? null }}
            </div>
        </div>
    </div>
</header>

<header class="flex items-center justify-between max-w-screen-xl mx-auto p-4 space-x-4 md:py-6 md:px-8">

</header>
