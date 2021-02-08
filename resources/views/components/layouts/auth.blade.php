<x-filament::layouts.base :title="__($title)">
    <main class="flex h-screen items-center justify-center p-4">
        <div class="w-full max-w-sm space-y-8">
            <header class="text-center">
                <h2 class="text-2xl md:text-3xl leading-tight text-red-700">{{ __($title) ?? config('app.name') }}</h2>
            </header>

            {{ $slot }}

            <footer class="flex items-center justify-center">
                <x-filament::branding-footer />
            </footer>
        </div>
    </main>
</x-filament::layouts.base>
