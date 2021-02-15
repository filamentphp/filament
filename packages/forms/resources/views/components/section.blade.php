@props([
    'fields' => [],
    'columns' => 1,
])

<div {{ $attributes }}>
    @if (count($fields))
        <x-forms::grid :columns="$columns">
            @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
        </x-forms::grid>
    @endif

    {{ $slot }}
</div>
