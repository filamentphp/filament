<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-values-summary grid gap-y-1 px-3 py-4'])
    }}
>
    @if (filled($label = $getLabel()))
        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $label }}
        </span>
    @endif

    @if ($state = $getState())
        <ul
            @class([
                'list-inside list-disc' => $isBulleted(),
            ])
        >
            @foreach ($state as $stateItem)
                <li class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $formatState($stateItem) }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
