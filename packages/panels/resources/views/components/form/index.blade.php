<form
    x-data="{ isProcessingField: false }"
    x-on:submit="if (isProcessingField) $event.preventDefault()"
    x-on:field-processing-started="isProcessingField = true"
    x-on:field-processing-finished="isProcessingField = false"
    {{ $attributes->class(['fi-form grid gap-y-6']) }}
>
    {{ $slot }}
</form>
