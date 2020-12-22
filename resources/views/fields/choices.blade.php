<div class="space-y-2">
    <ol>
        @foreach ($options as $options)
            <li>
                {{ $options->render() }}
            </li>
        @endforeach
    </ol>
    <x-filament::error :field="$model" />
</div>