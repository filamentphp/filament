<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'filament-tables-color-column flex flex-wrap gap-1',
                'px-4 py-3' => ! $isInline(),
            ])
    }}
>
    @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
        @php
            $itemIsCopyable = $isCopyable($state);
            $copyableState = $copyableState($state);
            $copyMessage = $getCopyMessage($state);
            $copyMessageDuration = $getCopyMessageDuration($state);
        @endphp

        <div
            @if ($state)
                style="background-color: {{ $state }}"
                @if ($itemIsCopyable)
                    x-on:click="
                        window.navigator.clipboard.writeText(@js($copyableState))
                        $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                    "
                @endif
            @endif
            @class([
                'relative flex h-6 w-6 rounded-md',
                'cursor-pointer' => $itemIsCopyable,
            ])
        ></div>
    @endforeach
</div>
