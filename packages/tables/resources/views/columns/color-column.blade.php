@php
    $state = $getState();
    $isCopyable = $isCopyable();
@endphp

<div
    @if ($state)
        style="background-color: {{ $state }}"
        @if ($isCopyable)
            x-on:click="
                window.navigator.clipboard.writeText(@js($getCopyableState()))
                $tooltip(@js($getCopyMessage()), { timeout: @js($getCopyMessageDuration()) })
            "
        @endif
    @endif
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'filament-tables-color-column relative ml-4 flex h-6 w-6 rounded-md',
                'cursor-pointer' => $isCopyable,
            ])
    }}
></div>
