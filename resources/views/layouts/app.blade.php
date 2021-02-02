@extends('filament::layouts.base')

@section('title', __($title))

@section('content')
    <a href="#content" class="sr-only">Skip to content</a>

    <div class="relative overflow-hidden"
         role="group"
         tabindex="-1"
         x-data="{ headerIsOpen: false }"
         @keydown.window.escape="headerIsOpen = false"
         x-on:resize.window="if (window.outerWidth > 768) headerIsOpen = false"
    >
        <div class="w-56 fixed z-20 h-screen transform transition-transform duration-200 md:translate-x-0 flex"
             :class="headerIsOpen ? 'translate-x-0' : '-translate-x-full'">
            <x-filament::app-header class="flex-grow overflow-y-auto" />

            <button
                type="button"
                aria-controls="banner"
                @click.prevent="headerIsOpen = false"
                :aria-expanded="headerIsOpen"
                x-cloak
                x-show.opacity="headerIsOpen"
                class="md:hidden absolute top-2 right-0 transform translate-x-full p-3 text-gray-200 hover:text-white transition-colors duration-200">
                <x-heroicon-o-x class="w-6 h-6" />
            </button>
        </div>

        <span class="absolute z-10 inset-0 bg-gray-800 bg-opacity-50 md:hidden" x-cloak x-show="headerIsOpen"
              @click="headerIsOpen = false"></span>

        <div class="min-h-screen w-full md:pl-56 flex flex-col">
            <header class="p-4 md:p-6 flex justify-between items-center space-x-4">
                <div class="flex items-center">
                    <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = true"
                            :aria-expanded="headerIsOpen"
                            class="md:hidden text-blue-600 hover:text-blue-800 transition-colors duration-200 mr-4">
                        <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
                    </button>

                    <h1 class="font-light text-2xl leading-tight text-red-700">{{ __($title) }}</h1>
                </div>

                {{ $actions ?? null }}
            </header>

            <main id="content" class="flex-grow max-w-full pb-6 px-4 md:px-6">
                {{ $slot }}
            </main>
            <footer rel="contentinfo" class="p-4 md:px-6 text-center">
                <x-filament::branding-footer />
            </footer>
        </div>
    </div>
@overwrite
