<img
    src="{{ $column->getPath($record) }}"
    class="{{ $column->rounded ? 'rounded-full' : null }}"
    style="
        {!! $column->getHeight() !== null ? "height: {$column->getHeight()};" : null !!};
        {!! $column->getWidth() !== null ? "width: {$column->getWidth()};" : null !!};
    "
>
