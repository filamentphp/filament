<x-filament::layouts.base :title="$title">
    <main class="flex h-screen items-center justify-center p-4">
        <div class="w-full max-w-sm space-y-8">
            <x-filament::branding.auth :title="$title ?? config('app.name')" />

            {{ $slot }}

            <footer class="flex items-center justify-center">
                <x-filament::branding.footer />
            </footer>
        </div>
    </main>
</x-filament::layouts.base>
