<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-color flex flex-wrap gap-1.5',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
        @php
            $itemIsCopyable = $isCopyable($state);
            $copyableState = $getCopyableState($state) ?? $state;
            $copyMessage = $getCopyMessage($state);
            $copyMessageDuration = $getCopyMessageDuration($state);
        @endphp

        <div
            @if ($itemIsCopyable)
                x-on:click="
                    window.navigator.clipboard.writeText(@js($copyableState))
                    $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                "
            @endif
            @class([
                'fi-ta-color-item h-6 w-6 rounded-md',
                'cursor-pointer' => $itemIsCopyable,
            ])
            @style([
                "background-color: {$state}" => $state,
            ])
        ></div>
    @endforeach
</div>
