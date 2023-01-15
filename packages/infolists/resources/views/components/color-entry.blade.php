<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
        $isCopyable = $isCopyable();
    @endphp

    <div
        @if ($state)
            style="background-color: {{ $state }}"
            @if ($isCopyable)
                x-data="{}"
                x-on:click="
                    window.navigator.clipboard.writeText(@js($state))
                    $tooltip(@js($getCopyMessage()), { timeout: @js($getCopyMessageDuration()) })
                "
            @endif
        @endif
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'filament-infolists-color-entry relative ml-4 flex h-6 w-6 rounded-md',
                    'cursor-pointer' => $isCopyable,
                ])
        }}
    >
    </div>
</x-dynamic-component>
