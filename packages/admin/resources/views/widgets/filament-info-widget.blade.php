<x-filament::card>
    <div class="flex items-center justify-between h-full">
        <div class="w-full space-y-6">
            <div class="flex items-center justify-between w-full space-x-4 rtl:space-x-reverse">
                <a href="https://filamentadmin.com" target="_blank" class="transition-colors duration-200 hover:text-primary-700">
{{--                    <x-filament::logo class="h-auto w-28" />--}}
                </a>

                @if ($version = \Filament\Facades\Filament::getVersion())
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
