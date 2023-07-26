<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-text-summary grid px-4 py-3 gap-y-1'])
    }}
>
    @if (filled($label = $getLabel()))
        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $label }}
        </span>
    @endif

    <span class="text-sm text-gray-500 dark:text-gray-400">
        {{ $formatState($getState()) }}
    </span>
</div>
