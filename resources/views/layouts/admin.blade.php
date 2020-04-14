@extends('filament::layouts.app')

@section('content')
    <div class="h-screen flex overflow-hidden bg-gray-100 dark:bg-gray-900" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">    
        {{-- Off-canvas menu for mobile --}}
        <div class="md:hidden">
            <div @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black opacity-0 pointer-events-none transition-opacity ease-linear duration-300" :class="{'opacity-50 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"></div>
            <div class="fixed inset-y-0 left-0 flex flex-col z-40 max-w-xs w-full bg-gray-800 transform ease-in-out duration-300 -translate-x-full" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                <div class="absolute top-3 right-0 -mr-16">
                    <button x-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center h-12 w-12 transition ease-in-out duration-150 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark-hover:text-gray-300 focus:outline-none">
                        <x-heroicon-o-x class="h-6 w-6" />
                    </button>
                </div>
                <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <x-filament-logo class="w-10 mr-3" />
                        <h2 class="text-lg leading-5 font-medium text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @include('filament::partials.nav', ['nav' => config('filament.nav.admin')])
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('filament::partials.user-actions')
                </div>
            </div>
        </div>   
        {{-- Static sidebar for desktop --}}
        <div class="hidden flex-shrink-0 md:flex">
            <div class="flex flex-col w-64 bg-gray-800">
                <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <x-filament-logo class="w-10 mr-3" />
                        <h2 class="text-lg leading-5 font-medium text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @include('filament::partials.nav', ['nav' => config('filament.nav.admin')])
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('filament::partials.user-actions')
                </div>
            </div>
        </div>
        {{-- Main content --}}
        <main id="content" class="flex-grow max-w-full focus:outline-none" tabindex="0">
            <div class="max-w-7xl mx-auto h-screen overflow-auto scrolling-touch">
                <div class="my-4 sm:my-8">
                    <header class="flex items-center justify-between mb-4 sm:mb-8 px-4 sm:px-8">
                        <div class="flex-grow">
                            <h1 class="text-xl lg:text-2xl font-semibold">@yield('title')</h1>
                        </div>
                        <ul class="flex-shrink-0 flex items-center">
                            <li>
                                @yield('actions')
                            </li>
                            <li class="ml-4">
                                @include('filament::partials.dark-mode-toggle')
                            </li>
                            <li class="ml-4">
                                <button @click.stop="sidebarOpen = true" class="inline-flex items-center transition ease-in-out duration-150 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark-hover:text-gray-300 focus:outline-none md:hidden">
                                    <x-heroicon-o-menu-alt-1 class="h-6 w-6" />
                                </button>
                            </li>
                        </ul>
                    </header>       
                    <div class="px-4 sm:px-8">
                        @yield('main')
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
