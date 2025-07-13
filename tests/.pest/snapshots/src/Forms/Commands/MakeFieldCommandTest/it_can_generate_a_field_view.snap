<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
    >
        {{-- Interact with the `state` property in Alpine.js --}}
    </div>
</x-dynamic-component>
