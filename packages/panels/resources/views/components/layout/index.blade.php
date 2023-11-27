@php
    use Filament\Support\Enums\MaxWidth;

    $navigation = filament()->getNavigation();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-layout flex min-h-screen w-full overflow-x-clip">
        <div
            x-cloak
            x-data="{}"
            x-on:click="$store.sidebar.close()"
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.300ms
            class="fi-sidebar-close-overlay fixed inset-0 z-30 bg-gray-950/50 transition duration-500 dark:bg-gray-950/75 lg:hidden"
        ></div>

        <x-filament-panels::sidebar :navigation="$navigation" />

        <div
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{}"
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @elseif (filament()->isSidebarFullyCollapsibleOnDesktop())
                x-data="{}"
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @elseif (! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()))
                x-data="{}"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @endif
            @class([
                'fi-main-ctn w-screen flex-1 flex-col',
                'h-full opacity-0 transition-all' => filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop(),
                'opacity-0' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()),
                'flex' => filament()->hasTopNavigation(),
            ])
        >
            <x-filament-panels::topbar :navigation="$navigation" />

            <main
                @class([
                    'fi-main mx-auto h-full w-full px-4 md:px-6 lg:px-8',
                    $maxContentWidth?->value ?? (filament()->getMaxContentWidth() ?? MaxWidth::SevenExtraLarge)->value,
                ])
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::content.start') }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::content.end') }}
            </main>

            {{ \Filament\Support\Facades\FilamentView::renderHook('panels::footer') }}
        </div>
    </div>
</x-filament-panels::layout.base>
