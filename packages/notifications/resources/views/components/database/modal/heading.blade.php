@props([
    'unreadNotificationsCount',
])

<x-notifications::modal.heading class="relative">
    <span>
        {{ __('notifications::database.modal.heading') }}
    </span>

    @if ($unreadNotificationsCount)
        <span @class([
            'inline-flex absolute items-center justify-center top-0 ml-1 min-w-[1rem] h-4 rounded-full text-xs text-primary-700 bg-primary-500/10',
            'dark:text-primary-500' => config('tables.dark_mode'),
        ])>
            {{ $unreadNotificationsCount }}
        </span>
    @endif
</x-notifications::modal.heading>
