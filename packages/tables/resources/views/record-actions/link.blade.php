@if ($action = $recordAction->getAction($record))
    <button
        wire:click="{{ $action }}('{{ $record->getKey() }}')"
        type="button"
        class="inline-flex items-center font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
        @if($title = $recordAction->getTitle())
            title="{{ $title }}"
        @endif
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </button>
@elseif ($url = $recordAction->getUrl($record))
    <a
        href="{{ $url }}"
        class="inline-flex items-center font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700"
        @if($title = $recordAction->getTitle())
            title="{{ $title }}"
        @endif
        @if ($recordAction->shouldOpenUrlInNewTab())
            target="_blank"
            rel="noopener noreferrer"
        @endif
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </a>
@else
    <span
        class="inline-flex items-center font-medium text-primary-600"
        @if($title = $recordAction->getTitle())
            title="{{ $title }}"
        @endif
    >
        @if ($recordAction->hasIcon())
            <x-dynamic-component :component="$recordAction->getIcon()" class="w-4 h-4 mr-1 inline" />
        @endif

        {{ __($recordAction->getLabel()) }}
    </span>
@endif
