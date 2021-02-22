@props([
    'form',
])

<form
    wire:submit.prevent="{{ $form->submitMethod }}"
    {{ $attributes }}
>
    <x-forms::grid :columns="$form->columns">
        @foreach ($form->schema as $component)
            {{ $component->render() }}
        @endforeach
    </x-forms::grid>

    {{ $slot }}
</form>
