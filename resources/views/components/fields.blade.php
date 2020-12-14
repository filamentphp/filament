@props([
    'fields' => [],
    'class' => 'space-y-6',
])

<div {{ $attributes }} class="{{ $class }}">
    @foreach ($fields as $field)
        {{ $field->render() }}
    @endforeach
</div>