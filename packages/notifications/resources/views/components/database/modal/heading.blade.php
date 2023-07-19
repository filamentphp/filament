@props([
    'unreadNotificationsCount',
])

<x-filament::modal.heading>
    <span class="relative">
        {{ __('filament-notifications::database.modal.heading') }}

        @if ($unreadNotificationsCount)
            <x-filament::badge size="xs" class="absolute -top-1 left-full ms-1">
                {{ $unreadNotificationsCount }}
            </x-filament::badge>
        @endif
    </span>
</x-filament::modal.heading>
