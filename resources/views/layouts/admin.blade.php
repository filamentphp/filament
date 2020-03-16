@extends('alpine::layouts.app', ['classes' => ['body' => '']])

@section('content')
    <div class="h-screen flex overflow-hidden bg-gray-100" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">
        <!-- Off-canvas menu for mobile -->
        <div class="md:hidden">
            <div @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-600 opacity-0 pointer-events-none transition-opacity ease-linear duration-300" :class="{'opacity-75 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"></div>
            <div class="fixed inset-y-0 left-0 flex flex-col z-40 max-w-xs w-full bg-gray-800 transform ease-in-out duration-300 -translate-x-full" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                <div class="absolute top-0 right-0 -mr-14 p-1">
                    <button x-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
                        {{ Alpine::svg('heroicons/outline-md/md-x', 'h-6 w-6 text-white') }}
                    </button>
                </div>
                <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                    <div class="flex-shrink-0 flex items-center px-4">
                        {{ Alpine::svg('logo', 'h-8 w-auto rounded-full mr-4') }}
                        <h2 class="text-xl leading-5 font-bold text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @if (config('alpine.nav.admin'))
                        <nav class="mt-5 flex-1 px-2 bg-gray-800">
                            @foreach(config('alpine.nav.admin') as $nav)
                                <a href="{{ Route::has($nav['url']) ? route('alpine.admin.dashboard') : $nav['url'] }}" class="@unless($loop->first) mt-2 @endunless group flex items-center px-2 py-2 text-sm leading-5 font-medium rounded-md transition ease-in-out duration-150 @isset($nav['active']) {{ active($nav['active'], 'text-white bg-gray-900', 'text-gray-300') }} @else text-gray-300 @endisset focus:text-white focus:outline-none focus:bg-gray-700 hover:text-white hover:bg-gray-900">
                                    @if (isset($nav['icon']))
                                        {{ Alpine::svg($nav['icon'], 'mr-3 h-6 w-6 text-gray-400') }}
                                    @endif
                                    {{ __($nav['label']) }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('alpine::partials.user-actions')
                </div>
            </div>
        </div>
        
        <!-- Static sidebar for desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-gray-800">
                <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        {{ Alpine::svg('logo', 'h-8 w-auto rounded-full mr-4') }}
                        <h2 class="text-xl leading-5 font-bold text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @if (config('alpine.nav.admin'))
                        <nav class="mt-5 flex-1 px-2 bg-gray-800">
                            @foreach(config('alpine.nav.admin') as $nav)
                                <a href="{{ Route::has($nav['url']) ? route('alpine.admin.dashboard') : $nav['url'] }}" class="@unless($loop->first) mt-2 @endunless group flex items-center px-2 py-2 text-sm leading-5 font-medium rounded-md transition ease-in-out duration-150 @isset($nav['active']) {{ active($nav['active'], 'text-white bg-gray-900', 'text-gray-300') }} @else text-gray-300 @endisset focus:text-white focus:outline-none focus:bg-gray-700 hover:text-white hover:bg-gray-900">
                                    @if (isset($nav['icon']))
                                        {{ Alpine::svg($nav['icon'], 'mr-3 h-6 w-6 text-gray-400') }}
                                    @endif
                                    {{ __($nav['label']) }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('alpine::partials.user-actions')
                </div>
            </div>
        </div>
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
                <button @click.stop="sidebarOpen = true" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:bg-gray-200 transition ease-in-out duration-150">
                    {{ Alpine::svg('heroicons/outline-md/md-menu-alt-1', 'h-6 w-6') }}
                </button>
            </div>
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 focus:outline-none md:py-6" tabindex="0" x-data x-init="$el.focus()">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('title')</h1>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Replace with your content -->
                    <div class="py-4">
                        @yield('main')
                    </div>
                    <!-- /End replace -->
                </div>
            </main>
        </div>
    </div>

@endsection