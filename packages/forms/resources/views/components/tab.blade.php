<div
    aria-labelledby="{{ "{$formComponent->getParent()->getId()}.{$formComponent->getId()}" }}"
    id="{{ "{$formComponent->getParent()->getId()}.{$formComponent->getId()}-tab" }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ "{$formComponent->getParent()->getId()}.{$formComponent->getId()}" }}'"
    class="p-4 md:p-6"
>
    <x-forms::layout :schema="$formComponent->getSchema()" :columns="$formComponent->getColumns()" />
</div>
