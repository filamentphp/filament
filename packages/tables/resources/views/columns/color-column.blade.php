@php
    $state = $getState();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class(['filament-tables-color-column relative ml-4 flex h-6 w-6 rounded-md'])
    }}
    @if ($state)
        style="background-color: {{ $state }}"
        @if ($isCopyable())
            x-on:click="
                window.navigator.clipboard.writeText(@js($state))
                $tooltip(@js($getCopyMessage()), { timeout: @js($getCopyMessageDuration()) })
            "
        @endif
    @endif
>
</div>
