<div
    aria-labelledby="{{ "{$formComponent->getId()}" }}"
    id="{{ "{$formComponent->getId()}-tab" }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ "{$formComponent->getId()}" }}'"
    class="p-4 md:p-6"
>
    <x-forms::form :schema="$formComponent->getSchema()" :columns="$formComponent->getColumns()" />
</div>
