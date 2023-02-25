@php
    $id = $getId();
@endphp

<div
    x-bind:class="{ 'invisible h-0 p-0 overflow-y-hidden': tab !== '{{ $id }}', 'p-6': tab === '{{ $id }}' }"
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['filament-infolists-tabs-component-tab outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
