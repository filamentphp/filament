<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'filament-infolists-color-entry flex flex-wrap gap-1',
                ])
        }}
    >
        @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
            @php
                $itemIsCopyable = $isCopyable($state);
                $copyableState = $copyableState($state) ?? $state;
                $copyMessage = $getCopyMessage($state);
                $copyMessageDuration = $getCopyMessageDuration($state);
            @endphp

            <div
                @if ($state)
                    style="background-color: {{ $state }}"
                    @if ($itemIsCopyable)
                        x-data="{}"
                        x-on:click="
                            window.navigator.clipboard.writeText(@js($copyableState))
                            $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                        "
                    @endif
                @endif
                @class([
                    'filament-infolists-color-entry-content relative flex h-6 w-6 rounded-md',
                    'cursor-pointer' => $itemIsCopyable,
                ])
            ></div>
        @endforeach
    </div>
</x-dynamic-component>
