@props([
    'form',
])

<form
    wire:submit.prevent="{{ $form->submitMethod }}"
    {{ $attributes }}
>
    <x-forms::grid :columns="$form->columns">
        @foreach ($form->fields as $field)
            {{ $field->render() }}
        @endforeach
    </x-forms::grid>

    {{ $slot }}
</form>
