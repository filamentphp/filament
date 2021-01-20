@props([
    'fields' => [],
    'columns' => 1,
])

<div {{ $attributes }} class="grid grid-cols-1 lg:grid-cols-{{ $columns }} gap-6">
    @foreach ($fields as $field)
        {{ $field->render() }}
    @endforeach
</div>
