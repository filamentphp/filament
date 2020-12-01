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
        <x-filament::app-header />
        <div class="flex-grow flex flex-col">
            <header class="p-4 md:p-6 flex justify-between items-center space-x-4">
                <div class="flex items-center">
                    <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = true" :aria-expanded="headerIsOpen" class="md:hidden text-blue-600 hover:text-blue-800 transition-colors duration-200 mr-4">
                        <x-heroicon-o-menu-alt-2 class="w-6 h-6" />
                    </button>
                    <h1 class="font-light text-2xl leading-tight text-red-700">{{ $title }}</h1>
                </div>
                {{ $actions ?? null }}
            </header>
            <main id="content" class="flex-grow px-4 md:px-6 flex flex-col">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-filament::notification />
@endsection