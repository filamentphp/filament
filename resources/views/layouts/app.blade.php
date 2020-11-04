@extends('filament::layouts.base')

@section('title', $title)

@section('content')
    <a href="#content" class="sr-only">Skip to content</a>
    <div class="relative overflow-hidden flex min-h-screen" 
        role="group" 
        tabindex="-1" 
        x-data="{ headerIsOpen: false }" 
        x-on:keydown.escape="headerIsOpen = false"
        x-on:resize.window="if (window.outerWidth > 768) headerIsOpen = false">
        <header role="banner" 
            class="bg-gray-900 text-gray-500 w-56 flex flex-col space-y-4 shadow-lg md:shadow-none absolute z-10 inset-y-0 md:static transform transition-transform duration-200 md:translate-x-0" 
            :class="headerIsOpen ? 'translate-x-0' : '-translate-x-full'" 
            tabindex="-1"
            id="banner">
            <a href="{{ url('/') }}" rel="home" class="px-4 py-3 flex items-center space-x-4 transition-colors duration-200 hover:text-white" target="_blank" rel="noopener noreferrer">
                <x-filament::app-icon class="w-10 h-10 fill-current" />
                <span class="text-sm leading-tight font-semibold">{{ config('app.name') }}</span>
            </a>
            <x-filament::nav />
            <x-filament::dropdown class="w-full text-left flex-grow flex items-center p-4 space-x-3 text-gray-600 transition-colors duration-200 hover:text-white hover:bg-gray-800" id="dropdown-user">
                <img src="{{ Auth::user()->avatar(32) }}" alt="{{ Auth::user()->name }}" srcset="{{ Auth::user()->avatar(32) }} 1x, {{ Auth::user()->avatar(64) }} 2x" class="flex-shrink-0 w-8 h-8 rounded" />
                <span class="flex-grow text-sm leading-tight font-semibold">{{ Auth::user()->name }}</span>

                <x-slot name="content">
                    <ul class="list-dropdown" aria-label="{{ __('User actions') }}">
                        {{-- <li><a href="#">{{ __('Edit Profile') }}</a></li> --}}
                        <li><livewire:filament-logout /></li>
                    </ul>
                </x-slot>
            </x-filament::dropdown>
            <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = false" :aria-expanded="headerIsOpen" x-cloak x-show.opacity="headerIsOpen" class="md:hidden absolute top-0 right-0 transform translate-x-full -translate-y-2 p-3 text-gray-400 hover:text-white transition-colors duration-200">
                <x-heroicon-o-x class="w-6 h-6" />
            </button>
        </header>
        <span class="absolute z-0 inset-0 bg-black opacity-50 md:hidden flex items-start justify-end" x-cloak x-show="headerIsOpen" @click="headerIsOpen = false"></span>
        <div class="flex-grow flex flex-col">
            <header class="p-4 md:p-6 flex justify-between items-center space-x-4">
                <div class="flex items-center">
                    <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = true" :aria-expanded="headerIsOpen" class="md:hidden text-blue-500 hover:text-blue-800 transition-colors duration-200 mr-4">
                        <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
                    </button>
                    <h1 class="font-light text-2xl leading-tight text-orange-700">{{ $title }}</h1>
                </div>
                {{ $actions ?? null }}
            </header>
            <main id="content" class="flex-grow flex flex-col">
                {{ $slot }}
            </main>
            <footer role="contentinfo" class="p-4 md:p-6 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-500">{{ config('filament.name') }}</span>
            </footer>
        </div>
    </div>
@endsection