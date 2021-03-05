<a
    href="{{ $column->getValue($record) }}"
    target="_blank"
    @if ($column->cover)
        class="block bg-center {{ $column->getClass() }}"
        style="
            {{ $column->getWidth() ? "width: {$column->getWidth()};" : '' }}
            {{ $column->getHeight() ? "height: {$column->getHeight()};" : '' }}
            background-image: url({{ $column->getValue($record) }});"
    @endif
>
    @unless ($column->cover)
        <img
            src="{{ $column->getValue($record) }}"
            class="{{ $column->getClass() }}"
            style="
                {{ $column->getWidth() ? "width: {$column->getWidth()};" : '' }}
                {{ $column->getHeight() ? "height: {$column->getHeight()};" : '' }}"
        >
    @endunless
</a>
