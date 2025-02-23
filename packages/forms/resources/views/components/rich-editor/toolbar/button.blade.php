@props([
    'activeOptions' => [],
    'type' => null,
])

<button
    @if ($type)
        x-bind:class="{
            'fi-active': editorUpdatedAt && getEditor().isActive(@js($type), @js($activeOptions)),
        }"
    @endif
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-fo-rich-editor-toolbar-btn'])
    }}
>
    {{ $slot }}
</button>
