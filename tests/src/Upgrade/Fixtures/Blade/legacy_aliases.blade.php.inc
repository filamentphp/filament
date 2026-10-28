<x-filament::page :full-height="$fullHeight" data-test="A > B" {{ $attributes }}>
    Don't change the content or surrounding whitespace.
    <x-filament::widget :data-example="'<x-filament::page />'">
        <x-filament::section>
            <x-slot name="heading">Named heading</x-slot>
            {{ $content }}
        </x-filament::section>
    </x-filament::widget>
</x-filament::page>
<x-filament::page/><x-filament::widget />
<x-filament::widget
    wire:key="summary"
/>
<x:filament::page></x:filament::page>
<x-filament-panels::page /><x-filament-widgets::widget />
<x-filament::page-header /><x-filament::page.header /><x-filament::widget-custom />
<x-filament::section /><x-other::page />
<x-dynamic-component component="filament::page" />
<x-dynamic-component :component="$widgetComponent" />
<div data-example="<x-filament::page />">Literal attribute</div>
{{-- <x-filament::page /><x-filament::widget /> --}}
@verbatim
<x-filament::page /><x-filament::widget />
@endverbatim
@php
    $component = 'filament::page';
    $template = '<x-filament::widget />';
@endphp
@if (0 < count($items))
    <x-filament::widget />
@endif
@if (0 < count($items))
    <x-filament::page>Content after comparison</x-filament::page>
@endif
<x-filament::page @class([count($items) < 2 ? 'compact' : 'wide'])>
    Content with conditional classes
</x-filament::page>
<x-filament::widget @style([count($items) < 2 ? 'width: 50%' : 'width: 100%']) />
<x-filament::page {{ $attributes->class([count($items) < 2 ? 'compact' : 'wide']) }}>
    Content with an attribute bag
</x-filament::page>
<x-filament::widget {{ $attributes->class([count($items) < 2 ? 'compact' : 'wide']) }} />
-----
<x-filament-panels::page :full-height="$fullHeight" data-test="A > B" {{ $attributes }}>
    Don't change the content or surrounding whitespace.
    <x-filament-widgets::widget :data-example="'<x-filament::page />'">
        <x-filament::section>
            <x-slot name="heading">Named heading</x-slot>
            {{ $content }}
        </x-filament::section>
    </x-filament-widgets::widget>
</x-filament-panels::page>
<x-filament-panels::page/><x-filament-widgets::widget />
<x-filament-widgets::widget
    wire:key="summary"
/>
<x:filament-panels::page></x:filament-panels::page>
<x-filament-panels::page /><x-filament-widgets::widget />
<x-filament::page-header /><x-filament::page.header /><x-filament::widget-custom />
<x-filament::section /><x-other::page />
<x-dynamic-component component="filament::page" />
<x-dynamic-component :component="$widgetComponent" />
<div data-example="<x-filament::page />">Literal attribute</div>
{{-- <x-filament::page /><x-filament::widget /> --}}
@verbatim
<x-filament::page /><x-filament::widget />
@endverbatim
@php
    $component = 'filament::page';
    $template = '<x-filament::widget />';
@endphp
@if (0 < count($items))
    <x-filament-widgets::widget />
@endif
@if (0 < count($items))
    <x-filament-panels::page>Content after comparison</x-filament-panels::page>
@endif
<x-filament-panels::page @class([count($items) < 2 ? 'compact' : 'wide'])>
    Content with conditional classes
</x-filament-panels::page>
<x-filament-widgets::widget @style([count($items) < 2 ? 'width: 50%' : 'width: 100%']) />
<x-filament-panels::page {{ $attributes->class([count($items) < 2 ? 'compact' : 'wide']) }}>
    Content with an attribute bag
</x-filament-panels::page>
<x-filament-widgets::widget {{ $attributes->class([count($items) < 2 ? 'compact' : 'wide']) }} />
