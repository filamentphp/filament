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
        <x-filament::form-grid :columns="$columns">
            @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
        </x-filament::form-grid>
    @endif

    {{ $slot }}
</form>
