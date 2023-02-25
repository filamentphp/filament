<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-color-entry flex flex-wrap gap-1',
        ])
    }}>
        @php
            $isCopyable = $isCopyable();
            $copyMessage = $getCopyMessage();
            $copyMessageDuration = $getCopyMessageDuration();
        @endphp

        @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
            <div
                @if ($state)
                    style="background-color: {{ $state }}"
                    @if ($isCopyable)
                        x-on:click="
                            window.navigator.clipboard.writeText(@js($state))
                            $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                        "
                    @endif
                @endif
                @class([
                    'relative flex h-6 w-6 rounded-md',
                    'cursor-pointer' => $isCopyable,
                ])
            >
            </div>
        @endforeach
    </div>
</x-dynamic-component>
