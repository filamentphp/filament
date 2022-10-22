<div {{ $attributes->class(['flex flex-col items-center justify-center mx-auto my-6 space-y-4 text-center bg-white dark:bg-gray-800']) }}>
    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-50 dark:bg-gray-700">
        <x-filament::icon
            name="heroicon-o-bell"
            alias="filament-notifications::database.modal.empty-state"
            color="text-primary-500"
            size="h-6 w-6"
        />
    </div>

    <div class="max-w-md space-y-1">
        <h2 class="text-lg font-bold tracking-tight dark:text-white">
            {{ __('filament-notifications::database.modal.empty.heading') }}
        </h2>

        <p class="whitespace-normal text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ __('filament-notifications::database.modal.empty.description') }}
        </p>
    </div>
</div>
