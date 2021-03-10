@if ($recordAction->action)
    <button
        wire:click="{{ $recordAction->action }}('{{ $record->getKey() }}')"
        type="button"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
    >
        {{ __($recordAction->label) }}
    </button>
@elseif ($recordAction->url)
    <a
        href="{{ $recordAction->getUrl($record) }}"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
        @if ($recordAction->shouldOpenUrlInNewTab)
        target="_blank"
        rel="noopener noreferrer"
        @endif
    >
        {{ __($recordAction->label) }}
    </a>
@else
    <span>{{ __($recordAction->label) }}</span>
@endif
