@if ($action = $recordAction->getAction($record))
    <button
        wire:click="{{ $action }}('{{ $record->getKey() }}')"
        {!! $recordAction->getTitle() ? 'title="' . __($recordAction->getTitle()) . '"' : null !!}
        type="button"
        class="inline-flex items-center font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </button>
@elseif ($url = $recordAction->getUrl($record))
    <a
        href="{{ $url }}"
        {!! $recordAction->getTitle() ? 'title="' . __($recordAction->getTitle()) . '"' : null !!}
        @if ($recordAction->shouldUrlOpenInNewTab())
            target="_blank"
            rel="noopener noreferrer"
        @endif
        class="inline-flex items-center font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </a>
@else
    <span
        class="inline-flex items-center font-medium text-primary-600"
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </span>
@endif
