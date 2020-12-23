<div class="space-y-2">
    <ol>
        @foreach ($field->options as $options)
            <li>
                {{ $options->render() }}
            </li>
        @endforeach
    </ol>
    <x-filament::error :field="$field->model" />
</div>