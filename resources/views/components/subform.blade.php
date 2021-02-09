@props([
    'fields' => [],
    'columns' => 1,
])

<div {{ $attributes }}>
    @if (count($fields))
        <x-filament::form-grid :columns="$columns">
            @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
        </x-filament::form-grid>
    @endif

    {{ $slot }}
</div>
