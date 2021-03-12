@props([
    'form',
])

<form
    wire:submit.prevent="{{ $form->getSubmitMethod() }}"
    {{ $attributes }}
>
    <x-forms::layout :schema="$form->getSchema()" :columns="$form->getColumns()" />

    {{ $slot }}
</form>
