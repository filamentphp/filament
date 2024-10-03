<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $arrayState = $getState();

        if ($arrayState instanceof \Illuminate\Support\Collection) {
            $arrayState = $arrayState->all();
        }

        $arrayState = \Illuminate\Support\Arr::wrap($arrayState);
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-color flex flex-wrap gap-1.5',
                ])
        }}
    >
        @if (count($arrayState))
            @foreach ($arrayState as $state)
                @php
                    $itemIsCopyable = $isCopyable($state);
                    $copyableState = $getCopyableState($state) ?? $state;
                    $copyMessage = $getCopyMessage($state);
                    $copyMessageDuration = $getCopyMessageDuration($state);
                @endphp

                <div
                    @if ($itemIsCopyable)
                        x-data="{}"
                        x-on:click="
                            window.navigator.clipboard.writeText(@js($copyableState))
                            $tooltip(@js($copyMessage), {
                                theme: $store.theme,
                                timeout: @js($copyMessageDuration),
                            })
                        "
                    @endif
                    @class([
                        'fi-in-color-item h-6 w-6 rounded-md',
                        'cursor-pointer' => $itemIsCopyable,
                    ])
                    @style([
                        'background-color: ' . e($state) => $state,
                    ])
                ></div>
            @endforeach
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <x-filament-infolists::entries.placeholder>
                {{ $placeholder }}
            </x-filament-infolists::entries.placeholder>
        @endif
    </div>
</x-dynamic-component>
