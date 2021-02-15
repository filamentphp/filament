@props([
    'fields' => [],
    'columns' => 1,
    'submit' => 'submit',
])

<form
    wire:submit.prevent="{{ $submit }}"
    {{ $attributes }}
>
    @if (count($fields))
        <x-forms::grid :columns="$columns">
            @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
        </x-forms::grid>
    @endif

    {{ $slot }}
</form>
