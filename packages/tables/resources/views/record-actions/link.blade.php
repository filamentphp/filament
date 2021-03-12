@if ($recordAction->action)
    <button
        wire:click="{{ $recordAction->action }}('{{ $record->getKey() }}')"
        type="button"
        title="{{$recordAction->label}}"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700 pr-2"
    >
    @if ($recordAction->display != 'text')
        <x-dynamic-component component="{{ $recordAction->icon}}" class="flex-shrink-0 h-5 w-5 inline" />
    @endif
    @if ($recordAction->display != 'icon')
        {{ __($recordAction->label) }}
    @endif
    </button>
@elseif ($recordAction->url)
    <a
        href="{{ $recordAction->getUrl($record) }}"
        class="font-medium transition-colors duration-200 text-primary-600 hover:underline hover:text-primary-700 pr-2"
        title="{{$recordAction->label}}"
        @if ($recordAction->shouldOpenUrlInNewTab)
        target="_blank"
        rel="noopener noreferrer"
        @endif
    >
        @if ($recordAction->display != 'text')
        <x-dynamic-component component="{{ $recordAction->icon}}" class="flex-shrink-0 h-5 w-5 inline" />
        @endif
        @if ($recordAction->display != 'icon')
        {{ __($recordAction->label) }}
        @endif
    </a>
@else
    <span>{{ __($recordAction->label) }}</span>
@endif
