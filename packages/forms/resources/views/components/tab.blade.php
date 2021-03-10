<div
    aria-labelledby="{{ "{$formComponent->parent->getId()}.{$formComponent->getId()}" }}"
    id="{{ "{$formComponent->parent->getId()}.{$formComponent->getId()}-tab" }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ "{$formComponent->parent->getId()}.{$formComponent->getId()}" }}'"
    class="p-4 md:p-6"
>
    <x-forms::layout :schema="$formComponent->schema" :columns="$formComponent->columns" />
</div>
