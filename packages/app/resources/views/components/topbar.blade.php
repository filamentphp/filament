@props([
    'breadcrumbs' => [],
])

<header {{ $attributes->class(['filament-main-topbar sticky top-0 z-10 flex h-16 w-full shrink-0 items-center border-b bg-white dark:bg-gray-800 dark:border-gray-700']) }}>
    <div class="flex items-center w-full px-2 sm:px-4 md:px-6 lg:px-8">
        <button
            x-cloak
            x-data="{}"
            x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
            @class([
                'filament-sidebar-open-button shrink-0 flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
                'lg:mr-4 rtl:lg:mr-0 rtl:lg:ml-4' => \Filament\Navigation\Sidebar::$isCollapsibleOnDesktop,
                'lg:hidden' => ! (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop && (\Filament\Navigation\Sidebar::$isFullyCollapsibleOnDesktop)),
            ])
        >
            <x-filament::icon
                name="heroicon-o-bars-3"
                alias="app::topbar.buttons.toggle-sidebar"
                color="text-primary-500"
                size="h-6 w-6"
            />
        </button>

        <div class="flex items-center justify-between flex-1">
            <x-filament::layouts.app.topbar.breadcrumbs :breadcrumbs="$breadcrumbs" />

            @livewire('filament.core.global-search')

            @livewire('filament.core.notifications')

            <x-filament::user-menu />
        </div>
    </div>
</header>
