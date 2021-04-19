<div>
    <x-filament::app-header :title="$title" />

    <x-filament::app-content>
        <section
            aria-label="{{ __('filament::widgets.title') }}"
            class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8"
        >
            @if (config('filament.widgets.default.account', true))
                <x-filament::card class="flex">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <x-filament-avatar :user="\Filament\Filament::auth()->user()" :size="160" class="flex-shrink-0 w-20 h-20 rounded-full" />

                        <div class="space-y-1">
                            <h2 class="text-2xl">{{ __('filament::dashboard.widgets.account.heading', ['name' => \Filament\Filament::auth()->user()->name]) }}</h2>
                            <p class="text-sm"><a href="{{ route('filament.account') }}" class="link">{{ __('filament::dashboard.widgets.account.links.account.label') }}</a></p>
                        </div>
                    </div>
                </x-filament::card>
            @endif

            @if (config('filament.widgets.default.info', true))
                <x-filament::card>
                    <div class="flex items-center justify-between h-full">
                        <div class="w-full space-y-6">
                            <div class="flex items-center justify-between w-full space-x-4 rtl:space-x-reverse">
                                <a href="https://filamentadmin.com" target="_blank" class="transition-colors duration-200 hover:text-primary-700">
                                    <x-filament::logo class="h-auto w-28" />
                                </a>

                                @if ($version = \Filament\Filament::version())
                                    <a href="https://github.com/laravel-filament/filament/releases" target="_blank" class="px-4 py-2 font-mono text-xs text-gray-800 transition duration-200 bg-white border border-gray-300 rounded shadow-sm cursor-pointer focus:ring focus:ring-opacity-50 hover:bg-gray-100 focus:ring-primary-200">
                                        {{ $version }}
                                    </a>
                                @endif
                            </div>

                            <ul class="space-y-1 text-sm">
                                <li><a href="https://filamentadmin.com/docs" target="_blank" class="link">{{ __('filament::dashboard.widgets.filament.links.documentation.label') }}</a></li>
                                <li><a href="https://github.com/laravel-filament/filament" target="_blank" class="link">{{ __('filament::dashboard.widgets.filament.links.repository.label') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </x-filament::card>
            @endif

            @foreach (\Filament\Filament::getWidgets() as $widget)
                @livewire(\Livewire\Livewire::getAlias($widget))
            @endforeach
        </section>
    </x-filament::app-content>
</div>
