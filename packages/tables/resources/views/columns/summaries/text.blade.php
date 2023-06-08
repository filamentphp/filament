<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['filament-tables-text-summary px-4 py-3 text-sm']) }}
>
    @if (filled($label = $getLabel()))
        <span class="text-gray-500 dark:text-gray-400">{{ $label }}:</span>
    @endif

    <span>
        {{ $formatState($getState()) }}
    </span>
</div>
