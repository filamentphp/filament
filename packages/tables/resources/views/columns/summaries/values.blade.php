<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'filament-tables-values-summary whitespace-normal px-4 py-3 text-sm',
                'prose prose-sm max-w-none dark:prose-invert' => $isBulleted(),
            ])
    }}
>
    @if (filled($label = $getLabel()))
        <p class="text-gray-500 dark:text-gray-400">{{ $label }}:</p>
    @endif

    <ul>
        @foreach ($getState() as $stateItem)
            <li>
                {{ $formatState($stateItem) }}
            </li>
        @endforeach
    </ul>
</div>
