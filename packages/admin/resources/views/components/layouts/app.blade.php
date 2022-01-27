<x-filament::layouts.base :title="$title">
    <div class="flex min-h-screen w-full">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="fixed inset-0 z-20 w-full h-full bg-gray-900/50 lg:hidden"
        ></div>

        <x-filament::layouts.app.sidebar />

        <div class="w-screen space-y-6 flex-1 flex flex-col lg:pl-80 rtl:lg:pl-0 rtl:lg:pr-80">
            <header class="h-[4rem] shrink-0 w-full border-b flex items-center dark:bg-dark-800 dark:border-dark-700">
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
                    <button x-data="{}" x-on:click="$store.sidebar.open()" class="shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none lg:hidden">
                        <x-heroicon-o-menu class="w-6 h-6" />
                    </button>

                    <div class="flex-1 flex items-center justify-between">
                        <div>
                            <ul class="hidden gap-4 items-center font-medium text-sm lg:flex dark:text-white">
                                @foreach ($breadcrumbs as $url => $label)
                                    <li>
                                        <a
                                            href="{{ is_int($url) ? '#' : $url }}"
                                            @class([
                                                'text-gray-500 dark:text-dark-400' => $loop->last,
                                            ])
                                        >
                                            {{ $label }}
                                        </a>
                                    </li>

                                    @if (! $loop->last)
                                        <li class="h-6 border-r border-gray-300 -skew-x-12 dark:border-dark-500"></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        @livewire('filament.core.global-search')

                        @if(config('filament.layout.darkmode'))
                        <div class="flex justify-center" x-data="darkmode">
                            <button x-show="theme === 'dark'" x-on:click="switchTheme('system')">
                                    <svg class="p-2 ml-3 w-8 h-8 text-gray-100 bg-dark-700 rounded-md transition cursor-pointer dark:hover:bg-dark-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>    </button>

                            <button x-show="theme === 'light'" x-on:click="switchTheme('dark')" x-cloak>
                                <svg class="p-2 ml-3 w-8 h-8 text-gray-700 bg-gray-200 rounded-md transition cursor-pointer hover:bg-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <button x-show="theme === 'system'" x-on:click="window.matchMedia('(prefers-color-scheme: dark)').matches ? switchTheme('light') : switchTheme('dark')" x-cloak>
                                <svg x-show="! window.matchMedia('(prefers-color-scheme: dark)').matches" class="p-2 ml-3 w-8 h-8 text-gray-700 bg-gray-100 rounded-md transition cursor-pointer hover:bg-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="display: none;">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                                <svg x-show="window.matchMedia('(prefers-color-scheme: dark)').matches" class="p-2 ml-3 w-8 h-8 text-gray-100 bg-dark-700 rounded-md transition cursor-pointer dark:hover:bg-dark-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </header>

            <div @class([
                'flex-1 w-full px-4 mx-auto md:px-6 lg:px-8',
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

            <div class="py-4 shrink-0">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>
