<x-filament::layouts.base :title="$title">
    <div class="flex min-h-screen w-full filament-app-layout">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="fixed inset-0 z-20 w-full h-full bg-gray-900/50 lg:hidden filament-sidebar-close-overlay"
        ></div>

        <x-filament::layouts.app.sidebar />

        @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
            <div
                x-data="{}"
                class="hidden transition-all w-screen space-y-6 flex-1 flex flex-col filament-main lg:pl-80 rtl:lg:pl-0 rtl:lg:pr-80"
                x-bind:class.="{
                    '!block': true, // Prevent flash, x-cloak not working with charts
                    'lg:pl-[5.4rem] rtl:lg:pr-[5.4rem]': ! $store.sidebar.isOpen
                }"
            >
        @else
            <div class="w-screen space-y-6 flex-1 flex flex-col lg:pl-80 rtl:lg:pl-0 rtl:lg:pr-80 filament-main">
        @endif
            <header @class([
                'h-[4rem] shrink-0 w-full border-b flex items-center filament-main-topbar',
                'dark:bg-gray-800 dark:border-gray-700' => config('filament.dark_mode'),
            ])>
                <div @class([
                    'flex items-center w-full px-2 mx-auto sm:px-4 md:px-6 lg:px-8',
                    match (config('filament.layout.max_content_width')) {
                        'xl' => 'max-w-xl',
                        '2xl' => 'max-w-2xl',
                        '3xl' => 'max-w-3xl',
                        '4xl' => 'max-w-4xl',
                        '5xl' => 'max-w-5xl',
                        '6xl' => 'max-w-6xl',
                        'full' => 'max-w-full',
                        default => 'max-w-7xl',
                    },
                ])>
                    <button
                        x-data="{}"
                        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                        @class([
                            'shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full filament-sidebar-open-button hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
                            'lg:hidden' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
                            'lg:mr-4 rtl:lg:mr-0 rtl:lg:ml-4' => config('filament.layout.sidebar.is_collapsible_on_desktop'),
                        ])
                    >
                        <x-heroicon-o-menu class="w-6 h-6" />
                    </button>

                    <div class="flex-1 flex gap-4 items-center justify-between">
                        <x-filament::layouts.app.topbar.breadcrumbs :breadcrumbs="$breadcrumbs" />

                        @livewire('filament.core.global-search')

                        <x-filament::layouts.app.topbar.user-menu />
                    </div>
                </div>
            </header>

            <div @class([
                'flex-1 w-full px-4 mx-auto md:px-6 lg:px-8 filament-main-content',
                match (config('filament.layout.max_content_width')) {
                    'xl' => 'max-w-xl',
                    '2xl' => 'max-w-2xl',
                    '3xl' => 'max-w-3xl',
                    '4xl' => 'max-w-4xl',
                    '5xl' => 'max-w-5xl',
                    '6xl' => 'max-w-6xl',
                    'full' => 'max-w-full',
                    default => 'max-w-7xl',
                },
            ])>
                {{ $slot }}
            </div>

            <div class="py-4 shrink-0 filament-main-footer">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>
