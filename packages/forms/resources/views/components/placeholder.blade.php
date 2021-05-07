<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :label="$formComponent->getLabel()"
>
    <div class="flex">
        <span
            class="block w-full placeholder-gray-400 placeholder-opacity-100 px-3 py-2 cursor-default"
        >
            {{ $formComponent->getValue() }}
        </span>
    </div>
</x-forms::field-group>
