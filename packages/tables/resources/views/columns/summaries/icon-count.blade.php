<div {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['filament-tables-icon-count-summary text-sm space-y-1 px-4 py-3']) }}>
    @if (filled($label = $getLabel()))
        <p class="text-gray-500 dark:text-gray-400">
            {{ $label }}:
        </p>
    @endif

    @foreach ($getState() as $color => $icons)
        @foreach ($icons as $icon => $count)
            @if ($icon)
                <div class="flex items-center space-x-1">
                    <span>
                        {{ $count }}
                    </span>

                    <span class="text-gray-500 dark:text-gray-400">
                        &times;
                    </span>

                    <x-filament::icon
                        :name="$icon"
                        alias="tables::columns.summaries.icon-count"
                        :color="match ($color) {
                            'danger' => 'text-danger-500',
                            'gray', null => 'text-gray-500',
                            'info' => 'text-info-500',
                            'primary' => 'text-primary-500',
                            'secondary' => 'text-secondary-500',
                            'success' => 'text-success-500',
                            'warning' => 'text-warning-500',
                            default => $color,
                        }"
                        size="h-4 w-4"
                    />
                </div>
            @endif
        @endforeach
    @endforeach
</div>
