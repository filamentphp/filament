@props([
    'maxContentWidth' => null,
])

<x-filament::layouts.base :title="$title">
    <div class="filament-app-layout flex w-full min-h-screen overflow-x-clip">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="filament-sidebar-close-overlay fixed inset-0 z-20 w-full h-full bg-gray-900/50 lg:hidden"
        ></div>

        <x-filament::layouts.app.sidebar />

        <div
            @if (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop)
                x-data="{}"
                x-bind:class="{
                    'lg:pl-[var(--collapsed-sidebar-width)] rtl:lg:pr-[var(--collapsed-sidebar-width)]': ! $store.sidebar.isOpen,
                    'filament-main-sidebar-open lg:pl-[var(--sidebar-width)] rtl:lg:pr-[var(--sidebar-width)]': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @endif
            @class([
                'filament-main flex-col gap-y-6 w-screen flex-1 rtl:lg:pl-0',
                'hidden h-full transition-all' => \Filament\Navigation\Sidebar::$isCollapsibleOnDesktop,
                'flex lg:pl-[var(--sidebar-width)] rtl:lg:pr-[var(--sidebar-width)]' => ! \Filament\Navigation\Sidebar::$isCollapsibleOnDesktop,
            ])
        >
            <x-filament::topbar :breadcrumbs="$breadcrumbs" />

            <div class="filament-main-content flex-1 w-full max-w-7xl px-4 mx-auto md:px-6 lg:px-8">
                {{ filament()->renderHook('content.start') }}

                {{ $slot }}

                {{ filament()->renderHook('content.end') }}
            </div>

            <div class="filament-main-footer py-4 shrink-0">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>
