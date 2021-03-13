@if ($action = $recordAction->getAction($record))
    <button
        wire:click="{{ $action }}('{{ $record->getKey() }}')"
        type="button"
        class="font-medium transition-colors duration-200 text-primary-600 hover:text-primary-700"
    >
        <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 inline" />
    </button>
@elseif ($url = $recordAction->getUrl($record))
    <a
        href="{{ $url }}"
        class="font-medium transition-colors duration-200 text-primary-600 hover:text-primary-700"
        @if ($recordAction->shouldOpenUrlInNewTab())
            target="_blank"
            rel="noopener noreferrer"
        @endif
    >
        <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 inline" />
    </a>
@else
    <span
        class="inline-flex items-center font-medium text-primary-600"
    >
        <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 inline" />
    </span>
@endif
