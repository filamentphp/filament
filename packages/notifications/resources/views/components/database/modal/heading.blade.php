@props([
    'unreadNotificationsCount',
])

<x-filament::modal.heading class="relative">
    <span>
        {{ __('filament-notifications::database.modal.heading') }}
    </span>

    @if ($unreadNotificationsCount)
        <span
            class="absolute top-0 ms-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary-500/10 text-xs text-primary-700 dark:text-primary-500"
        >
            {{ $unreadNotificationsCount }}
        </span>
    @endif
</x-filament::modal.heading>
