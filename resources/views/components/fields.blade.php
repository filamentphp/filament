<div class="mb-4 grid grid-cols-4 gap-4">
    @foreach ($fields as $field)
        @if ($group)
            @if ($group === $field->group)
                {{ $field->render() }}
            @endif
        @else
            {{ $field->render() }}
        @endisset
    @endforeach
</div>
