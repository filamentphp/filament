@props([
    'fields' => [],
    'columns' => 1,
    'submit' => 'submit',
])

<form wire:submit.prevent="{{ $submit }}" {{ $attributes }} class="grid grid-cols-1 lg:grid-cols-{{ $columns }} gap-6">
    @foreach ($fields as $field)
        {{ $field->render() }}
    @endforeach
    
    {{ $slot }}
</form>
