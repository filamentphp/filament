@props([
    'embedded' => false,
    'fields' => [],
    'columns' => 1,
    'submit' => 'submit',
])

@if ($embedded)
    <div {{ $attributes }}>
@else
    <form
        wire:submit.prevent="{{ $submit }}"
        {{ $attributes }}
    >
@endif

@if (count($fields))
    <div class="grid grid-cols-1 lg:grid-cols-{{ $columns }} gap-6">
        @foreach ($fields as $field)
                {{ $field->render() }}
            @endforeach
    </div>
@endif

{{ $slot }}

@unless ($embedded)
    </form>
@else
    </div>
@endif
