<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-text-summary text-sm'])
    }}
>
    @if (filled($label = $getLabel()))
        <span class="text-gray-500 dark:text-gray-400">{{ $label }}:</span>
    @endif

    <span>
        {{ $formatState($getState()) }}
    </span>
</div>
