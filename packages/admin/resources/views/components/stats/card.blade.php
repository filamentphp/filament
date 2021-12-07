@props([
    'description' => null,
    'descriptionColor' => null,
    'descriptionIcon' => null,
    'flat' => false,
    'label' => null,
    'value' => null,
])

<div {{ $attributes->class([
    'p-6 rounded-2xl',
    'bg-white shadow' => ! $flat,
    'border' => $flat,
]) }}>
    <div class="space-y-2">
        <div class="text-sm font-medium text-gray-500">{{ $label }}</div>

        <div class="text-3xl">{{ $value }}</div>

        @if ($description)
            <div class="flex items-center space-x-1 text-sm font-medium {{ [
                null => 'text-gray-600',
                'danger' => 'text-danger-600',
                'primary' => 'text-primary-600',
                'success' => 'text-success-600',
                'warning' => 'text-warning-600',
            ][$descriptionColor] }}">
                <span>{{ $description }}</span>

                @if ($descriptionIcon)
                    <x-dynamic-component :component="$descriptionIcon" class="w-4 h-4" />
                @endif
            </div>
        @endif
    </div>
</div>
