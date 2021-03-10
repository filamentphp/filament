<div
    aria-labelledby="{{ "{$formComponent->parent->id}.{$formComponent->getId()}" }}"
    id="{{ "{$formComponent->parent->id}.{$formComponent->getId()}-tab" }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ "{$formComponent->parent->id}.{$formComponent->getId()}" }}'"
    class="p-4 md:p-6"
>
    <x-forms::layout :schema="$formComponent->schema" :columns="$formComponent->columns" />
</div>
