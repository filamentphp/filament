@php
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
@endphp

@if (filled($id) || filled($extraAttributes))
    {!! '<div' !!}
    {{-- Avoid formatting issues with unclosed elements --}}
    {{
        $attributes
            ->merge([
                'id' => $id,
            ], escape: false)
            ->merge($extraAttributes, escape: false)
    }}
    >
@endif

@if (filled($key = $getLivewireKey()))
    @livewire($getComponent(), $getComponentProperties(), key($key))
@else
    @livewire($getComponent(), $getComponentProperties())
@endif
@if (filled($id) || filled($extraAttributes))
    {!! '</div>' !!}
    {{-- Avoid formatting issues with unclosed elements --}}
@endif
