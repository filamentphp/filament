@extends('filament::layouts.app', ['classes' => ['body' => 'bg-gray-200 dark:bg-gray-900']])

@section('content')
    <div class="h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">    
        <div @click="sidebarOpen = false" class="fixed z-30 md:hidden inset-0 bg-black opacity-0 pointer-events-none transition-opacity ease-linear duration-300" :class="{'opacity-75 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"></div>
        <div class="fixed z-40 inset-y-0 left-0 w-64 bg-gray-800 flex flex-col transform ease-in-out duration-300 md:static md:flex-shrink-0" :class="{'translate-x-0': sidebarOpen, '-translate-x-full md:translate-x-0': !sidebarOpen}">
            <div x-show="sidebarOpen" class="absolute top-3 right-0 -mr-16 md:hidden">
                <button @click="sidebarOpen = false" class="flex items-center h-12 w-12 transition ease-in-out duration-150 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark-hover:text-gray-300 focus:outline-none">
                    <x-heroicon-o-x class="h-6 w-6" />
                </button>
            </div>
            <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4">
                    <x-filament-logo class="w-10 mr-3" />
                    <h2 class="text-lg leading-5 font-medium text-gray-200">
                        {{ config('app.name') }}
                    </h2>
                </div>
                <nav class="flex-1 py-5 px-2">
                    @foreach (config('filament.nav.admin') as $item)
                        @if (!isset($item['ability']) || auth()->user()->can($item['ability']))
                            <a href="{{ Route::has($item['url']) ? route($item['url']) : $item['url'] }}" 
                                class="@unless($loop->first) mt-2 @endunless group flex items-center px-3 py-2 text-sm leading-5 font-medium rounded transition ease-in-out duration-150 @isset($item['active']) {{ active($item['active'], 'text-gray-50 bg-gray-900', 'text-gray-300') }} @else text-gray-300 @endisset focus:text-gray-50 focus:outline-none focus:bg-gray-700 hover:text-gray-50 hover:bg-gray-900"
                                @isset($item['target'])
                                    target="{{ $item['target'] }}"
                                    @if ($item['target'] == '_blank')
                                        rel="noopener noreferrer"
                                    @endif
                                @endisset
                            >
                                @if (isset($item['icon']))
                                    {{ Filament::icon($item['icon'], 'mr-3 h-6 w-6 text-gray-400') }}
                                @endif
                                {{ __($item['label']) }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
            <div class="flex-shrink-0 flex bg-gray-700 p-4">
                @include('filament::partials.user-actions')
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
