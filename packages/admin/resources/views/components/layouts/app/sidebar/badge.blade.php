@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => null,
])

<span
    @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
        x-show="$store.sidebar.isOpen"
    @endif
    @class(
        array_merge(
            [
                'inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal',
                'text-white bg-white/20' => $active,
            ],
            match ($badgeColor) {
                'danger' => [
                    'text-danger-700 bg-danger-500/10' => ! $active,
                    'dark:text-danger-500' => (! $active) && config('tables.dark_mode'),
                ],
                'secondary' => [
                    'text-gray-700 bg-gray-500/10' => ! $active,
                    'dark:text-gray-500' => (! $active) && config('tables.dark_mode'),
                ],
                'success' => [
                    'text-success-700 bg-success-500/10' => ! $active,
                    'dark:text-success-500' => (! $active) && config('tables.dark_mode'),
                ],
                'warning' => [
                    'text-warning-700 bg-warning-500/10' => ! $active,
                    'dark:text-warning-500' => (! $active) && config('filament.dark_mode'),
                ],
                'primary', null => [
                    'text-primary-700 bg-primary-500/10' => ! $active,
                    'dark:text-primary-500' => (! $active) && config('tables.dark_mode'),
                ],
                default => [
                    $badgeColor => ! $active,
                ],
            },
        )
    )
>
    {{ $badge }}
</span>
