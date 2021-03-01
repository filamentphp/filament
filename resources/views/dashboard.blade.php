<div>
    <x-filament::app-header :title="__($title)" />

    <x-filament::app-content>
        <section
            aria-label="{{ __('filament::widgets.title') }}"
            class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8"
        >
            <x-filament::widget>
                <div class="flex space-x-8 items-center">
                    <x-filament-avatar :user="Auth::guard('filament')->user()" :size="300" class="flex-shrink-0 w-32 h-32 rounded-full" />

                    <div class="space-y-2">
                        <h2 class="text-xl font-medium">Welcome back, {{ Auth::guard('filament')->user()->name }}</h2>

                        <p>
                            <a href="{{ route('filament.account') }}" class="link">Manage your account</a>
                        </p>
                    </div>
                </div>
            </x-filament::widget>

            <x-filament::widget>
                <div class="h-full flex items-center justify-between">
                    <div class="space-y-6 w-full">
                        <div class="flex items-center space-x-4 justify-between w-full">
                            <a href="https://filamentadmin.com" target="_blank" class="text-primary-700 hover:text-primary-900 transition-colors duration-200">
                                <x-filament::logo class="w-36 h-auto" />
                            </a>

                            @if (class_exists('Composer\\InstalledVersions'))
                                <a href="https://github.com/laravel-filament/filament/releases" target="_blank" class="inline-flex items-center px-3 py-1 rounded font-medium bg-secondary-100 text-secondary-800">
                                    {{ Composer\InstalledVersions::getPrettyVersion('filament/filament') }}
                                </a>
                            @endif
                        </div>

                        <p>
                            <a href="https://filamentadmin.com/docs" target="_blank" class="link text-lg">Browse the documentation</a>
                        </p>

                        <p>
                            <a href="https://github.com/laravel-filament/filament" target="_blank" class="link text-lg">Visit the repository</a>
                        </p>
                    </div>
                </div>
            </x-filament::widget>
        </section>
    </x-filament::app-content>
</div>
