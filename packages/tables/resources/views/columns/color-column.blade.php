@php
    $isCopyable = $isCopyable();
    $copyMessage = $getCopyMessage();
    $copyMessageDuration = $getCopyMessageDuration();
@endphp

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
        <div
            @if ($state)
                style="background-color: {{ $state }}"
                @if ($isCopyable)
                    x-on:click="
                        window.navigator.clipboard.writeText(@js($getCopyableState()))
                        $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                    "
                @endif
            @endif
            @class([
                'relative flex h-6 w-6 rounded-md',
                'cursor-pointer' => $isCopyable,
            ])
        ></div>
    @endforeach
</div>
