@if ($column->getAction($record) !== null)
    <button
        wire:click="{{ $column->getAction($record) }}('{{ $record->getKey() }}')"
        type="button"
    >
        <img
            src="{{ $column->getPath($record) }}"
            class="{{ $column->rounded ? 'rounded-full' : null }}"
            style="
                {!! $column->getHeight() !== null ? "height: {$column->getHeight()};" : null !!}
                {!! $column->getWidth() !== null ? "width: {$column->getWidth()};" : null !!}
            "
        />
    </button>
@elseif ($column->getUrl($record) !== null)
    <a
        href="{{ $column->getUrl($record) }}"
        @if ($column->shouldOpenUrlInNewTab)
            target="_blank"
            rel="noopener noreferrer"
        @endif
    >
        <img
            src="{{ $column->getPath($record) }}"
            class="{{ $column->rounded ? 'rounded-full' : null }}"
            style="
                {!! $column->getHeight() !== null ? "height: {$column->getHeight()};" : null !!}
                {!! $column->getWidth() !== null ? "width: {$column->getWidth()};" : null !!}
            "
        />
    </a>
@else
    <img
        src="{{ $column->getPath($record) }}"
        class="{{ $column->rounded ? 'rounded-full' : null }}"
        style="
            {!! $column->getHeight() !== null ? "height: {$column->getHeight()};" : null !!}
            {!! $column->getWidth() !== null ? "width: {$column->getWidth()};" : null !!}
        "
    />
@endif
