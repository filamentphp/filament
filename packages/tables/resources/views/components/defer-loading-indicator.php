<div {{ $attributes->class([
    'filament-tables-empty-state flex flex-1 flex-col items-center justify-center p-6 mx-auto space-y-6 text-center bg-white',
    'dark:bg-gray-800' => config('tables.dark_mode'),
]) }}>
    <div @class([
        'flex items-center justify-center w-16 h-16 text-primary-500 rounded-full bg-primary-50',
        'dark:bg-gray-700' => config('tables.dark_mode'),
    ])>
        <x-filament-support::loading-indicator
            class="w-6 h-6"
        />
    </div>                
</div>