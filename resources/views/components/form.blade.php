@props([
    'embedded' => false,
    'fields' => [],
    'columns' => 1,
    'submit' => 'submit',
])

<{{ $embedded ? 'div' : "form wire:submit.prevent={$submit}" }} {{ $attributes }}>
    @if (count($fields))
        <div class="grid grid-cols-1 lg:grid-cols-{{ $columns }} gap-6">
            @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
        </div>
    @endif

    {{ $slot }}
</ {{ $embedded ? 'div' : 'form' }}>
