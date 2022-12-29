<div {{ $attributes
    ->merge($getExtraAttributes(), escape: false)
    ->class([
        'filament-tables-list-column whitespace-normal',
        'prose prose-sm max-w-none dark:prose-invert' => $isBulleted(),
    ])
}}>
    <ul>
        @foreach ($getState() as $stateItem)
            <li>
                {{ $formatState($stateItem) }}
            </li>
        @endforeach
    </ul>
</div>
