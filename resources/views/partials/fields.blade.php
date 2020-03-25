@isset($fields)
    @foreach ($fields as $field)
    @if ($field->view)
        @include($field->view)
    @else
        @include('filament::fields.' . $field->type)
    @endif
    @endforeach
@endisset