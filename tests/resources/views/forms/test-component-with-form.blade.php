<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
    >
        {{ $getChildComponentContainer() }}
    </div>
</x-dynamic-component>
