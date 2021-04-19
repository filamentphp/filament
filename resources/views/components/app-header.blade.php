@props([
    'breadcrumbs' => [],
    'title',
])

<header class="flex items-center justify-between max-w-screen-xl mx-auto p-4 space-x-4 rtl:space-x-reverse md:py-6 md:px-8">
    <div class="flex items-center">
        <button
            type="button"
            aria-controls="banner"
            @click.prevent="headerIsOpen = true"
            :aria-expanded="headerIsOpen"
            class="ltr:mr-4 rtl:ml-4 text-gray-500 transition-colors duration-200 md:hidden hover:text-gray-700"
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

            <h1 class="text-2xl font-medium leading-tight md:text-3xl">{{ __($title) }}</h1>
        </div>
    </div>

    {{ $actions ?? null }}
</header>
