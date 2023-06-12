@props([
    'unreadNotificationsCount',
])

<x-notifications::modal.heading class="relative">
    <span>
        {{ __('notifications::database.modal.heading') }}
    </span>

    @if ($unreadNotificationsCount)
        <span
            @class([
                'absolute top-0 ml-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary-500/10 text-xs text-primary-700',
                'dark:text-primary-500' => config('tables.dark_mode'),
            ])
        >
            {{ $unreadNotificationsCount }}
        </span>
    @endif
</x-notifications::modal.heading>
