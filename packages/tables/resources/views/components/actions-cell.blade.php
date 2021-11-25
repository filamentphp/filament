@props([
    'actions',
    'record',
])

<td {{ $attributes->class(['px-4 py-3 whitespace-nowrap']) }}>
    <div class="flex items-center justify-center gap-4">
        @foreach ($actions as $action)
            @if (! $action->record($record)->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </div>
</td>
