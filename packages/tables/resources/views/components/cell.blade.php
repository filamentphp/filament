@props([
    'action' => null,
    'name',
    'record',
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

<td {{ $attributes }}>
    @if ($action)
        <button
            @if (is_string($action))
                wire:click="{{ $action }}('{{ $record->getKey() }}')"
            @elseif ($action instanceof \Closure)
                wire:click="callTableColumnAction('{{ $name }}', '{{ $record->getKey() }}')"
            @endif
            type="button"
            class="block text-left"
        >
            {{ $slot }}
        </button>
    @elseif ($url)
        <a
            href="{{ $url }}"
            {{ $shouldOpenUrlInNewTab ? 'target="_blank"' : null }}
            class="block"
        >
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>
