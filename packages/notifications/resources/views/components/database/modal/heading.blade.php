@props([
    'unreadNotificationsCount',
])

<x-filament::modal.heading class="relative">
    {{ __('filament-notifications::database.modal.heading') }}

    @if ($unreadNotificationsCount)
        <x-filament::badge size="xs" class="absolute left-full top-0 ms-1">
            {{ $unreadNotificationsCount }}
        </x-filament::badge>
    @endif
</x-filament::modal.heading>
