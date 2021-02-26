@props([
    'form',
])

<form
    wire:submit.prevent="{{ $form->submitMethod }}"
    {{ $attributes }}
>
    <x-forms::layout :schema="$form->schema" :columns="$form->columns" />

    {{ $slot }}
</form>
