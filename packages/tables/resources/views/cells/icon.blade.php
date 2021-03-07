@foreach($column->options as $icon => $callback)
    @if($callback($column->getValue($record)))
        <x-dynamic-component :component="$icon" class="w-6 h-6" />
        @break
    @endif
@endforeach
