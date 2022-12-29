<div {{ $attributes
    ->merge($getExtraAttributes(), escape: false)
    ->class([
        'filament-tables-values-summary text-sm px-4 py-3 whitespace-normal',
        'prose prose-sm max-w-none' => $isBulleted(),
    ])
}}>
    @if (filled($label = $getLabel()))
        <p class="text-gray-500 dark:text-gray-400">
            {{ $label }}:
        </p>
    @endif

    <ul>
        @foreach ($getState() as $stateItem)
            <li>
                {{ $formatState($stateItem) }}
            </li>
        @endforeach
    </ul>
</div>
