@isset($fields)
    @foreach ($fields as $field)
        @isset ($group)
            @if ($group === $field->group)
                @include($field->getView())
            @endif
        @else
            @include($field->getView())
        @endisset
    @endforeach
@endisset

@pushonce('scripts')
    @include('filament::partials.file-upload')
@endpushonce