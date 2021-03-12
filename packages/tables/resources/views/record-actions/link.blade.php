@if ($action = $recordAction->getAction($record))
    <button
        wire:click="{{ $action }}('{{ $record->getKey() }}')"
        type="button"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
    >
        {{ __($recordAction->getLabel()) }}
    </button>
@elseif ($url = $recordAction->getUrl($record))
    <a
        href="{{ $url }}"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
        @if ($recordAction->shouldOpenUrlInNewTab())
            target="_blank"
            rel="noopener noreferrer"
        @endif
    >
        {{ __($recordAction->getLabel()) }}
    </a>
@else
    <span>{{ __($recordAction->getLabel()) }}</span>
@endif
