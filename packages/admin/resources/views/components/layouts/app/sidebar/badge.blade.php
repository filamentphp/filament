@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => null,
])

<span
    @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
        x-show="$store.sidebar.isOpen"
    @endif
    @class(array_merge(
        [
            'min-h-4 ml-auto inline-flex items-center justify-center whitespace-normal rounded-xl px-2 py-0.5 text-xs font-medium tracking-tight rtl:ml-0 rtl:mr-auto',
            'bg-white/20 text-white' => $active,
        ],
        match ($badgeColor) {
            'danger' => [
                'bg-danger-500/10 text-danger-700' => ! $active,
                'dark:text-danger-500' => (! $active) && config('tables.dark_mode'),
            ],
            'secondary' => [
                'bg-gray-500/10 text-gray-700' => ! $active,
                'dark:text-gray-500' => (! $active) && config('tables.dark_mode'),
            ],
            'success' => [
                'bg-success-500/10 text-success-700' => ! $active,
                'dark:text-success-500' => (! $active) && config('tables.dark_mode'),
            ],
            'warning' => [
                'bg-warning-500/10 text-warning-700' => ! $active,
                'dark:text-warning-500' => (! $active) && config('filament.dark_mode'),
            ],
            'primary', null => [
                'bg-primary-500/10 text-primary-700' => ! $active,
                'dark:text-primary-500' => (! $active) && config('tables.dark_mode'),
            ],
            default => [
                $badgeColor => ! $active,
            ],
        },
    ))
>
    {{ $badge }}
</span>
