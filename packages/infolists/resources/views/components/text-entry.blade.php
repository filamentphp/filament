<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['filament-infolists-text-entry']) }}>
        {{ $formatState($getState()) }}
    </div>
</x-dynamic-component>
