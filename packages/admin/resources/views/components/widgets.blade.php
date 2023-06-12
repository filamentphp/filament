@props([
    'columns' => [
        'lg' => 2,
    ],
    'data' => [],
    'widgets' => [],
])

<x-filament-support::grid
    :default="$columns['default'] ?? 1"
    :sm="$columns['sm'] ?? null"
    :md="$columns['md'] ?? null"
    :lg="$columns['lg'] ?? ($columns ? (is_array($columns) ? null : $columns) : 2)"
    :xl="$columns['xl'] ?? null"
    :two-xl="$columns['2xl'] ?? null"
    class="filament-widgets-container mb-6 gap-4 lg:gap-8"
>
    @foreach ($widgets as $widget)
        @if ($widget::canView())
            @livewire(\Livewire\Livewire::getAlias($widget), $data, key($widget))
        @endif
    @endforeach
</x-filament-support::grid>
