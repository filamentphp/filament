<x-filament::layouts.base :title="$title">
    <div class="flex min-h-screen w-full bg-gray-100">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="fixed inset-0 z-20 w-full h-full bg-gray-900/50 lg:hidden"
        ></div>

        <aside
            x-data="{}"
            x-cloak
            x-bind:class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-20 flex flex-col h-screen overflow-hidden shadow-2xl rounded-r-2xl transition duration-300 bg-white lg:border-r w-80 lg:z-0 lg:translate-x-0"
        >
            <header class="border-b h-[4rem] px-6 flex items-center">
                <a href="{{ \Filament\Pages\Dashboard::geturl() }}">
                    <x-filament::brand />
                </a>
            </header>

            <nav class="flex-1 py-6">
                <ul class="space-y-6 px-6">
                    @foreach (\Filament\Facades\Filament::getNavigation() as $group => $items)
                        <li>
                            @if ($group)
                                <p class="font-bold uppercase text-gray-600 text-xs tracking-wider">
                                    {{ $group }}
                                </p>
                            @endif

                            <ul @class([
                                    'text-sm space-y-1 -mx-3',
                                    'mt-2' => $group,
                                ])>
                                @foreach ($items as $item)
                                    @php
                                        $isActive = $item->isActive();
                                    @endphp
                                    <li>
                                        <a
                                            href="{{ $item->getUrl() }}"
                                            @class([
                                                'flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition',
                                                'hover:bg-gray-500/5 focus:bg-gray-500/5' => ! $isActive,
                                                'bg-primary-500 text-white' => $isActive,
                                            ])
                                        >
                                            <x-dynamic-component :component="$item->getIcon()" class="h-5 w-5" />

                                            <span>
                                                    {{ $item->getLabel() }}
                                                </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        @if (! $loop->last)
                            <li>
                                <div class="border-t -mr-6"></div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>

            <footer class="border-t px-6 py-3 flex items-center gap-3">
                @php
                    $user = \Filament\Facades\Filament::auth()->user();
                @endphp

                <div
                    class="w-11 h-11 rounded-full bg-gray-200 bg-cover bg-center"
                    style="background-image: url('{{ \Filament\Facades\Filament::getAvatarUrl($user) }}')"
                ></div>

                <div>
                    <p class="text-sm font-bold">
                        {{ $user->getFilamentName() }}
                    </p>

                    <p class="text-xs text-gray-500 transition hover:text-gray-700 focus:text-gray-700">
                        <a href="{{ route('filament.logout') }}">
                            Sign out
                        </a>
                    </p>
                </div>
            </footer>
        </aside>

        <div class="w-screen flex-1 flex flex-col lg:pl-80">
            <header class="h-[4rem] w-full border-b flex items-center">
                <div class="flex items-center w-full max-w-6xl px-2 mx-auto sm:px-4 md:px-6 lg:px-8">
                    <button x-data="{}" x-on:click="$store.sidebar.open()" class="flex-shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 transition rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none lg:hidden">
                        <x-heroicon-o-menu class="w-6 h-6" />
                    </button>

                    <div class="flex-1 flex items-center justify-between">
                        <div>
                            <ul class="hidden gap-4 items-center font-medium text-sm lg:flex">
                                @foreach ($breadcrumbs as $url => $label)
                                    <li>
                                        <a
                                            href="{{ is_int($url) ? '#' : $url }}"
                                            @class([
                                                'text-gray-500' => $loop->last,
                                            ])
                                        >
                                            {{ $label }}
                                        </a>
                                    </li>

                                    @if (! $loop->last)
                                        <li class="h-6 border-r border-gray-300 -skew-x-12"></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        @livewire('filament.global-search')
                    </div>
                </div>
            </header>

            <div class="max-w-6xl flex-1 w-full px-4 py-6 mx-auto md:px-6 lg:px-8">
                {{ $slot }}
            </div>

            <div class="py-8">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>
