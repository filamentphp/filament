@foreach ($fields as $field)
    @if ($group)
        @if ($group === $field->group)
            @include($field->getView())
        @endif
    @else
        @include($field->getView())
    @endisset
@endforeach
