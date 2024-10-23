@php
    $canWrap = $canWrap();

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
                'fi-ta-color flex gap-1.5',
                'flex-wrap' => $canWrap,
                'px-3 py-4' => ! $isInline(),
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
                    x-on:click="
                        window.navigator.clipboard.writeText(@js($copyableState))
                        $tooltip(@js($copyMessage), {
                            theme: $store.theme,
                            timeout: @js($copyMessageDuration),
                        })
                    "
                @endif
                @class([
                    'fi-ta-color-item h-6 w-6 rounded-md',
                    'cursor-pointer' => $itemIsCopyable,
                ])
                @style([
                    'background-color: ' . e($state) => $state,
                ])
            ></div>
        @endforeach
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-tables::columns.placeholder>
            {{ $placeholder }}
        </x-filament-tables::columns.placeholder>
    @endif
</div>
