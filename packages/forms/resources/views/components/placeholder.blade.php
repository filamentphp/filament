<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-placeholder-component']) }}>
        {{ $getContent() }}
    </div>
</x-dynamic-component>
