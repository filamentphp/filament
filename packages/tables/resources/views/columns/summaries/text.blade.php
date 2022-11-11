<div {{ $attributes->class(['filament-tables-text-summary text-sm px-4 py-3']) }}>
    @if (filled($label = $getLabel()))
        <span class="text-gray-500 dark:text-gray-400">
            {{ $label }}:
        </span>
    @endif

    <span>
        {{ $getFormattedState() }}
    </span>
</div>
