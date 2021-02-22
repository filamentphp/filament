@props([
    'columns' => 1,
    'schema' => [],
])

<div {{ $attributes }}>
    @if (count($schema))
        <x-forms::grid :columns="$columns">
            @foreach ($schema as $component)
                {{ $component->render() }}
            @endforeach
        </x-forms::grid>
    @endif

    {{ $slot }}
</div>
