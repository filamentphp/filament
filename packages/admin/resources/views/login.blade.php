<div @class([
    'flex items-center justify-center min-h-screen filament-login-page bg-gray-100 text-gray-900',
    'dark:bg-gray-900 dark:text-white' => config('filament.dark_mode'),
])>
    <div class="w-screen max-w-md px-6 -mt-16 space-y-8 md:mt-0 md:px-2">
        <form wire:submit.prevent="authenticate" @class([
            'p-8 space-y-8 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative',
            'dark:bg-gray-900/50 dark:border-gray-700' => config('filament.dark_mode'),
        ])>
            <div class="flex justify-center w-full">
                <x-filament::brand />
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-center">
                {{ __('filament::login.heading') }}
            </h2>

            {{ $this->form }}

            <x-filament::button type="submit" form="authenticate" class="w-full">
                {{ __('filament::login.buttons.submit.label') }}
            </x-filament::button>
        </form>
    </div>
</div>
