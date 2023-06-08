@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => null,
])

<span
    @if (filament()->isSidebarCollapsibleOnDesktop())
        x-show="$store.sidebar.isOpen"
    @endif
    @class([
        'min-h-4 ms-auto inline-flex items-center justify-center whitespace-normal rounded-xl px-2 py-0.5 text-xs font-medium tracking-tight',
        match ($active) {
            true => 'bg-gray-900/10 text-white',
            false => match ($badgeColor) {
                'danger' => 'bg-danger-500/10 text-danger-700 dark:text-danger-500',
                'gray' => 'bg-gray-500/10 text-gray-700 dark:text-gray-500',
                'info' => 'bg-info-500/10 text-info-700 dark:text-info-500',
                'primary', null => 'bg-primary-500/10 text-primary-700 dark:text-primary-500',
                'secondary' => 'bg-secondary-500/10 text-secondary-700 dark:text-secondary-500',
                'success' => 'bg-success-500/10 text-success-700 dark:text-success-500',
                'warning' => 'bg-warning-500/10 text-warning-700 dark:text-warning-500',
                default => $badgeColor,
            },
        },
    ])
>
    {{ $badge }}
</span>
