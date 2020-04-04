<div class="mb-4 grid grid-cols-4 gap-4">
    @foreach ($fields as $field)
        @if ($group)
            @if ($group === $field->group)
                @include($field->getView())
            @endif
        @else
            @include($field->getView())
        @endisset
    @endforeach
</div>
