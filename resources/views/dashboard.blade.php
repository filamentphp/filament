<div>
    <x-filament::app-header :title="__($title)" />

    <x-filament::app-content>
        <section
            aria-label="{{ __('filament::widgets.title') }}"
            class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8"
        >
            <x-filament::card class="flex">
                <div class="flex items-center space-x-4">
                    <x-filament-avatar :user="Auth::guard('filament')->user()" :size="160" class="flex-shrink-0 w-20 h-20 rounded-full" />

                    <div class="space-y-1">
                        <h2 class="text-2xl">Welcome, {{ Auth::guard('filament')->user()->name }}</h2>
                        <p class="text-sm"><a href="{{ route('filament.account') }}" class="link">Manage your account</a></p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between h-full">
                    <div class="w-full space-y-6">
                        <div class="flex items-center justify-between w-full space-x-4">
                            <a href="https://filamentadmin.com" target="_blank" class="transition-colors duration-200 text-primary-700 hover:text-primary-900">
                                <x-filament::logo class="h-auto w-28" />
                            </a>

                            @if (class_exists('Composer\\InstalledVersions'))
                                <a href="https://github.com/laravel-filament/filament/releases" target="_blank" class="px-4 py-2 font-mono text-xs text-gray-800 transition duration-200 bg-white border border-gray-300 rounded shadow-sm cursor-pointer focus:ring focus:ring-opacity-50 hover:bg-gray-100 focus:ring-primary-200">
                                    {{ Composer\InstalledVersions::getPrettyVersion('filament/filament') }}
                                </a>
                            @endif
                        </div>

                        <div class="prose-sm prose">
                            <ul>
                                <li><a href="https://filamentadmin.com/docs" target="_blank">Browse the documentation</a></li>
                                <li><a href="https://github.com/laravel-filament/filament" target="_blank">Visit the repository</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </x-filament::card>
        </section>
    </x-filament::app-content>
</div>
