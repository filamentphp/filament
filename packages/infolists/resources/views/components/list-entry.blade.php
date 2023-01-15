<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-list-entry whitespace-normal',
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
</x-dynamic-component>
